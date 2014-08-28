<?php defined('SYSPATH') OR die('No direct access allowed.');

abstract class Model_Places_Abstract_Place extends Model_Cloudshard {
	
	protected static $name;
    protected static $icon = 'default';
	protected static $namelist;
	protected static $description;
    protected static $outside = true;

	protected static $widget_list = Array(
				'zombie-radar',
				'mapper',
				'description',
			);
	
	protected static $weight_limit = NULL;

	protected $variant_name;
	
	protected $inventory;
	
	protected $zombie_factory;
	protected $item_factory;
	
	protected $log;

    protected $doorway = array();
    protected static $auto_doorways = array();

	protected static $auto_actions = Array();

	public function widget_list() {
		return static::$widget_list;
	}

    protected function create_npcs() {
        return array();
    }

    public function is_outside() {
        return static::$outside;
    }

    public function get_doorways() {
        return $this->doorway;
    }

    public function register_doorway($lid) {
        $this->doorway[] = $lid;
    }

    public function uin($uin = NULL) {
        if ($uin === NULL) return parent::uin();
        else $t = parent::uin($uin);

        global $game;

        //Register sub locations
        foreach (static::$auto_doorways as $dwid) {
            $slid = $game->register_map("submap_{$dwid}_{$uin}", $dwid);
            if ($slid) {
                $this->register_doorway($slid);
                $game->location($slid)->register_doorway($uin);
            }
        }

        return $t;
    }

    /**
     * @param mixed|null $id
     * @return null|Model_Npc|Model_Hid|[Model_NPC]
     */
    public function get_npc($id = null) {
        if ($id === null) return $this->create_npcs();

        $tmp = $this->create_npcs();
        if (isset($tmp[$id])) return $tmp[$id];
        else return null;
    }

    public function mapable() {
        return true;
    }
	
	protected $survival_find = false;
	
	//Create a new inventory and assign a variable name (if a namelist is present from which to choose)
	public function __construct() {
        /**
         * @global $game Model_Game
         */
		global $game;
			
		$this->inventory = new Model_Inventory;

		$this->log = new Model_Log_Log();
		$this->zombie_factory = new Model_Factory_Zombies(get_called_class(), $game->config('game.config.spawn'));
		
		$this->item_factory = new Model_Factory_Items(get_called_class(), $game->config('places.dryout_factor'), $game->config('game.config.itemset'));
		
		if (static::$namelist) {
            $list = array();
            for ($i = 0; $i < count(static::$namelist); $i++)
                if ($game->ndp_check(get_called_class(), $i))
                    $list[] = $i;

            if (!$list) {
               $game->ndp_purge(get_called_class());
                $type = mt_rand(0, count(static::$namelist) - 1);
            } else $type = $list[mt_rand(0, count($list) - 1)];

            $this->variant_name = static::$namelist[$type];
            $game->ndp_register(get_called_class(), $type);
        }
	}
	
	public function auto_actions() {
		return static::$auto_actions;
	}
	
	/**
	 * Returns local zombie factory
	 * @return Model_Factory_Zombies
	 */
	public function zombie_factory() {
		return $this->zombie_factory;
	}
	
	public function log() {
		return $this->log;
	}
	
	public function weight_limit() {
		return static::$weight_limit;
	}

	public function inventory() {
		return $this->inventory;
	}
	
	//Enter location
	public function can_enter($pid = null) {
		return true;
	}
	
	//Leave location
	public function can_leave($pid = null) {
        /**
         * @global $game Model_Game
         */
        global $game;
        return ($game->get_player($pid)->can_escape() || $this->zombie_factory->get_zombie_accumulation() <= 0);
	}
	
	//Enter location
	public function enter($pid = null) {
        /**
         * @global $game Model_Game
         */
		global $game;
		if (!$pid) global $player;
		else $player = $game->get_player($pid);
		$this->log->add(new Model_Log_Types_Movement(Model_Log_Types_Movement::MOVEMENT_TYPE_ENTER, $pid));
		if ($player->job(1060) && !$this->survival_find) {
			
			$this->survival_find = true;
			$findings = min(2,max(0,$player->job(false) - 2));
			
			if ($findings > 0) {
				$items = Array();
				for ($i = 0; $i < $findings; $i++) $items[] = $this->item_factory->spawn(true);
				Tool_Scripts::place_new_item($items, false, $this);
				$this->log->add(new Model_Log_Types_Item(Model_Log_Types_Item::MLTI_EAGLE, $items));
			}
		}
		
		return true;
	}
	
	//Leave location
	public function leave($pid = null) {
		$this->log->add(new Model_Log_Types_Movement(Model_Log_Types_Movement::MOVEMENT_TYPE_LEAVE, $pid));

		if (count(Tool_Scripts::at_location()) <= 1) $this->vacate();
		
		return true;
	}

    public function pass($pid = null) {
        $this->log->add(new Model_Log_Types_Movement(Model_Log_Types_Movement::MOVEMENT_TYPE_PASS, $pid));

        return true;
    }
	
	public function vacate() {
		foreach ($this->inventory->get('Interface_Tmpitem') as $item) $item->consume();
		$this->log->trim(5);
	}
	
	//Return name
	public function name() {
		return $this->variant_name ? $this->variant_name : static::$name;
	}

    /**
     * Will return item icon path
     * @return string
     */
    public function icon() {
        return "/application/assets/icons/places/" . static::$icon . ".gif";
    }
	
	public function find_item($force = false) {
        /**
         * @global $game Model_Game
         * @global $player Model_Player
         */
		global $game, $player;
		
		if ($player->buff_retr('fragile') || $player->buff_retr('passout')) return true;
		if ($item = $this->item_factory->spawn($force, 1, Tool_Scripts::calculate_find_chances($player->id())))
			Tool_Scripts::place_new_item($item);
        else return true;
		return 1;
	}

    public function hero_replensish() {
        $this->item_factory->replenish(0.75);
    }
	
	public function break_out($fight) {
        /**
         * @global $game Model_Game
         * @global $player Model_Player
         */
		global $game, $player;
		
		if (!$fight) {
			//Attempt to flee
			$c = $game->config('zombies.escape_threshold');
			for ($i = 0; $i < $this->zombie_factory->get_zombie_accumulation(); $i++) $c += mt_rand(0, ceil($this->zombie_factory->get_zombie_accumulation()/5));
			
			$c = ceil($c * (1 + ($player->stats_get(Model_Player::MP_STAT_DRUNK) / 100)));
			
			$item_list = Array();
			while ((($player->stats_get(Model_Player::MP_STAT_ENERGY) * $player->stats_get(Model_Player::MP_CHAR_EVASIVENESS)) < $c) && ($items = Tool_Scripts::available_items('Model_Items_Abstract_Escape')))
			{
				/** @var $items Model_Items_Abstract_Escape[] */
                $c -= $items[0]->escape();
				if (!isset($item_list[$items[0]->icon()])) $item_list[$items[0]->icon()] = 1;
				else $item_list[$items[0]->icon()]++;
				$items[0]->consume();
			}

            $game->stats(Model_Game::MGLS_Energy, -10);
			if (($player->stats_get(Model_Player::MP_STAT_ENERGY) * $player->stats_get(Model_Player::MP_CHAR_EVASIVENESS)) >= $c) {
				$c = $this->zombie_pop();
                $this->zombie_pop(true);
				$this->zombie_factory()->accumulate_zombies(ceil($c/(1.05 * $player->stats_get(Model_Player::MP_CHAR_BULKYNESS))));

				$player->enable_escape();
				
				$item_accum = Array();
				if (count($item_list) > 0)
					foreach ($item_list as $icon => $count) $item_accum[] = '<span class="value"><img src="' . $icon . '" alt="?" />{$count}</span>';

                $player->log()->add(new Model_Log_Types_Text('Erfolgreiche Flucht!', 'Du bist entkommen!', 'Schreiend und mit geschlossenen Augen rennst du auf die Zombies zu. Die sind von dieser Aktion so überrascht, dass du die meisten von ihnen einfach aus dem Weg stoßen kannst. ' . ((empty($item_accum)) ? '' : ('<br /><br />Die Zombies, die du nicht einfach wegstoßen kannst lenkst du durch den geschickten Einsatz folgender Gegenstände ab:<br />:items<br /><br />')) . 'Als du deine Augen wieder öffnest, stellst du fest, dass keine Zombies mehr in deiner Nähe sind.', array(':items' => implode(', ', $item_accum))));
				
				$player->achievements()->achieve(Model_Achievement::MA_CLOSE_ESCAPES);
				return true;
			} else $player->log()->add(new Model_Log_Types_Text('Fehlgeschlagene Flucht!', 'Der Kampf beginnt!', 'Schreiend und mit geschlossenen Augen rennst du auf die Zombies zu. Die sind von dieser Aktion so überrascht, dass du die meisten von ihnen einfach aus dem Weg stoßen kannst - aber leider nicht alle. Ein Zombie steht dir mitten im Weg, und wirft dich zu Boden als du versuchst, ihn umzurennen. Zwar kannst du schnell wieder aufspringen, bist nun aber von geifernden Zombies umzingelt. Flucht ist keine Option mehr, du wirst kämpfen müssen.'));
		} else $this->log->add(new Model_Log_Types_Text('Angriff', 'Der Kampf beginnt!', 'Du versteckst dich hinter einer Ecke und atmest ein paar mal tief durch. Als du genug Mut gesammelt hast, trittst du hervor und ziehst deine Waffen. Der Kampf kann beginnen!'));

        $battle_log = Tool_Scripts::battle($this->zombie_factory->release_zombie_population(), Tool_Scripts::at_location(), false, $battle, $zc);
        if ($battle_log) {
            /** @var Model_Battle_Battle $battle */
            $this->log->add(new Model_Log_Types_Battle('Du greifst die :zombiestr an, die den Weg versperren!', $battle_log, array(':zombiestr' => '<span class="value"><img src="/application/assets/icons/zombie.gif"></img>' . $zc . ' ' . __('Zombies') . '</span>')));
            $this->zombie_factory()->accumulate_zombies($battle->get_zombie_count());
        }

		return true;	
	}
	
	public function pretick() {
        global $game;

        //Check for zombie attack
        if ($ghuls = $game->get_ghuls($this->uin())) {
            $battle_log = Tool_Scripts::battle($ghuls, Tool_Scripts::at_location($this->uin), false, $battle, $zc, Model_Achievement::MA_MERCYKILL);
            /** @var Model_Battle_Battle $battle */
            if ($battle && $battle->get_zombie_count() == 0) {
                $this->log->add(new Model_Log_Types_Battle('Einer deiner zombifizierten Freunde greift an!', $battle_log));

                if ($battle->get_zombie_count() == 0)
                    foreach ($ghuls as $key => $data) {
                        $game->unregister_ghul($key);
                        $drop = array();
                        foreach ($data->inventory()->get() as $item) {
                            $drop[] = $item;
                            $this->inventory()->add($item);
                        }

                        $this->log()->add(new Model_Log_Types_Item(Model_Log_Types_Item::MLTI_GHULKILL, $drop, $data->get_id()));
                    }
            }
        } elseif ($battle_log = Tool_Scripts::battle($this->zombie_factory->spawn_zombies(), Tool_Scripts::at_location($this->uin), true, $battle, $zc)) {
            $this->log->add(new Model_Log_Types_Battle(':zombiestr tauchen auf!', $battle_log, array(':zombiestr' => '<span class="value"><img src="/application/assets/icons/zombie.gif"></img>' . $zc . ' ' . __('Zombies') . '</span>')));
            return;
        }
		
		//Accumulate zombies
		$this->zombie_factory->accumulate_zombies();
	}
	
	public function tick() {

		$this->find_item();
        $this->find_building();

		return true;
	}
	
	public function zombie_pop($reset = false) {
		if ($reset) $this->zombie_factory->reset_zombie_population();
		else return $this->zombie_factory->get_zombie_accumulation();
        return true;
	}
	
	//Return description
	public function description() {
		return static::$description;
	}
	
	//Interact with location
	public function interact($action, $argument) {
		$method = "interaction_{$action}";
		if (method_exists($this, $method)) $r = $this->$method($argument);
		else throw new Exception("Location method interaction_{$action} doesnt exist", 1);
		
		return $r;
	}
	
	public function interaction_mpa($project) {
        /**
         * @global $game Model_Game
         * @global $player Model_Player
         */
		global $game, $player;
		$buildcfg = Kohana::$config->load('blueprints.lda.' . get_called_class() . '.' . $project);

		if (!$buildcfg) return true;
	
		if ($game->requirements($buildcfg['energy'], $buildcfg['requires']))
		{
			foreach($buildcfg['produces'] as $class => $count) for ($i = 0; $i < $count; $i++) $this->inventory->add(new $class);
			if (isset($buildcfg['achievement']) && $buildcfg['achievement']) $player->achievements()->achieve($buildcfg['achievement']);


            if (isset($buildcfg['short']))
                $p = $buildcfg['short'];
            elseif (isset($buildcfg['produces'])) {
                $c = array_keys($buildcfg['produces']);
                $c = $c[0];
                /** @noinspection PhpUndefinedMethodInspection */
                $p = $c::static_name();
            } else $p = $buildcfg['text'];

            $this->log->add(new Model_Log_Types_Built(Model_Log_Types_Built::MLTB_VARIOUS,$p, $player->id()),null);
            $player->log()->add(isset($buildcfg['finalmsg']) ? $buildcfg['finalmsg'] : 'Arbeit in der Ruine abgeschlossen!');
		}
		return true;
	}

    protected function find_building() {
        /**
         * @global $game Model_Game
         * @global $player Model_Player
         */
        global $game, $player;

        if ($player->buff_retr('fragile')) return false;
        if (!($building = $game->map($this->uin())->attempt_unvail($this->uin(), $player->stats_get(Model_Player::MP_CHAR_LOCATION_SPAWNRATE)))) return true;

        //Mapper
        if ($game->config('modules.mapping') && ($items = $player->inventory()->get('Model_Items_Maptool'))) {
            /** @var $items Model_Items_Maptool[] */
            if (!($items = $player->inventory()->get('Model_Items_Maptool'))) return false;
            $items[0]->common_discovery(mt_rand(5, 15));
            $player->log()->add(new Model_Log_Types_Text(null, null, 'Du hast eine neue Ruine entdeckt und eine grobe Karte mit ihrer Position gezeichnet. Diese Informationen sind sicher nützlich für deine Stadt.... besser wäre es natürlich, du würdest diese Ruine genauer erkunden.'));
        }

        $this->log->add(new Model_Log_Types_Building($building));
        return true;
    }

    public function grind() {
        /**
         * @global $game Model_Game
         */
        global $game;
        $this->inventory()->grind();
        $game->uin()->remove($this->uin());
    }
}	