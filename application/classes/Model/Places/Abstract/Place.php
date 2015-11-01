<?php defined('SYSPATH') OR die('No direct access allowed.');

abstract class Model_Places_Abstract_Place extends Model_Cloudshard {
	
	protected static $name;
    protected static $icon = 'default';
	protected static $namelist;
	protected static $description;
    protected static $outside = true;

    protected static $custom_style = null;

    protected static $perpetualDaytime = null;

	protected static $widget_list = Array(
				'zombie-radar',
				'mapper',
				'description',
			);
	
	protected static $weight_limit = NULL;

	protected $variant_name;
	
	protected $inventory;

    protected $upgrades = [];

    /** @var Model_Factory_Zombies  */
	protected $zombie_factory;
    /** @var  Model_Factory_Items */
	protected $item_factory;
	
	protected $log;

    protected $doorway = array();
    protected static $auto_doorways = array();

	protected static $auto_actions = Array();

	public function widget_list() {
		return static::$widget_list;
	}

    public static function get_namelist() {
        return (static::$namelist) ? static::$namelist : [static::$name];
    }

    public static function getCustomStyle() {
        return static::$custom_style;
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

    public function getPerpetualDayTime() {
        return static::$perpetualDaytime;
    }

    public function get_upgrades() {
        return $this->upgrades;
    }

    public function add_upgrades($a) {
        if (is_array($a))
            foreach ($a as $elem)
                $this->add_upgrades($elem);
        elseif (!in_array($a,$this->upgrades))
            $this->upgrades[] = $a;
    }

    public function remove_upgrades($a) {
        if (!is_array($a))
            $a = [$a];
        $this->upgrades = array_filter($this->upgrades, function($elem) use ($a) {
            return !in_array($elem, $a);
        });
    }

    public function has_upgrade($a) {
        if (is_array($a)) {
            foreach ($a as $elem)
                if (!$this->has_upgrade($elem))
                    return false;
            return true;
        } else return in_array($a, $this->upgrades);
    }

    public function uin($uin = NULL) {
        if ($uin === NULL) return parent::uin();
        else $t = parent::uin($uin);

        /** @global Model_Game $game */
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
		/** @var Model_Factory_Zombies zombie_factory */
        $this->zombie_factory = Model_Factory_Zombies::read(get_called_class(), $game->config('game.config.spawn'));
        $this->item_factory = Model_Factory_Items::read(get_called_class(), $game->config('game.config.itemset'))->modify_decay($game->config('places.dryout_factor'));
		
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
	public function can_leave($pid = null, $ignore_zombies = false) {
        /**
         * @global $game Model_Game
         */
        global $game;
        return ($ignore_zombies || $game->get_player($pid)->can_escape() || $this->zombie_factory->accumulation() <= 0);
	}

    //Enter map
    public function can_enter_map($pid = null) {
        return $this->can_enter($pid);
    }

    //Leave map
    public function can_leave_map($pid = null) {
        return $this->can_leave();
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
				$items = [];
				for ($i = 0; $i < $findings; $i++)
                    if ($find = $this->item_factory->spawn(true))
                        $items[] = $find;
				if ($items) {
                    Tool_Scripts::place_new_item($items, false, $this);
                    $this->log->add(new Model_Log_Types_Item(Model_Log_Types_Item::MLTI_EAGLE, $items));
                }
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

    //Enter map
    public function enter_map($pid = null) {
        return $this->enter($pid);
    }

    //Leave map
    public function leave_map($pid = null) {
        return $this->leave($pid);
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
        return static::$icon . ".gif";
    }

    /**
     * @param bool $force
     * @param bool $return
     * @return bool|Model_Items_Abstract_Item|null
     * @throws Exception
     */
	public function find_item($force = false, $return = false) {
        /**
         * @global $game Model_Game
         * @global $player Model_Player
         */
		global $game, $player;

        // Spawn ticket
        if (!$return && Tool_Events::ticket_event($game->next_tick()) && !$player->golden_ticket()) {
            $num = max(1,mt_rand(1,3) - mt_rand(0,2));
            $tmp = array();
            for ($i = 0; $i < $num; $i++)
                $tmp[] = new Model_Items_Generic_Ticket();

            Tool_Scripts::place_new_item($tmp);
            $player->golden_ticket(true);
        }

        // Spawn BrainCoins
        if (!$return && Tool_Gambling::random(Tool_Scripts::getBrainCoinLikelinessLevel($this->uin())))
            Tool_Scripts::place_new_item(new Model_Items_Braincoin());


		if (!$return && ($player->buff_retr('fragile') || $player->buff_retr('passout'))) return true;
		$item = $this->item_factory->spawn($force, true, Tool_Scripts::calculate_find_chances($player->id()));
        if ($item && !$return) {
            Tool_Scripts::place_new_item($item);
            return true;
        }
        elseif ($item && $return) return $item;
        elseif (!$item && $return) return null;
        else return true;
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
			for ($i = 0; $i < $this->zombie_factory->accumulation(); $i++) $c += mt_rand(0, ceil($this->zombie_factory->accumulation()/5));
			
			$c = ceil($c * (1 + ($player->stats_get(Model_Player::MP_STAT_DRUNK) / 100)));
			
			$item_list = Array();
			while ((($player->stats_get(Model_Player::MP_STAT_ENERGY) * $player->stats_get(Model_Player::MP_CHAR_EVASIVENESS)) < $c) && ($items = Tool_Scripts::available_items('Model_Items_Abstract_Escape')))
			{
				/** @var $items Model_Items_Abstract_Escape[] */
                $c -= $items[0]->escape();
				if (!isset($item_list[$items[0]->name()])) $item_list[$items[0]->name()] = 1;
				else $item_list[$items[0]->name()]++;
				$items[0]->consume();
			}

            $player->stats_modify(Model_Player::MP_STAT_ENERGY, -10);
			if (($player->stats_get(Model_Player::MP_STAT_ENERGY) * $player->stats_get(Model_Player::MP_CHAR_EVASIVENESS)) >= $c) {
				$c = $this->zombie_pop();
                $this->zombie_pop(true);
				$this->zombie_factory()->accumulation(ceil($c/(1.05 * $player->stats_get(Model_Player::MP_CHAR_BULKYNESS))));

				$player->enable_escape();
				
				$item_accum = Array();
				if (count($item_list) > 0)
					foreach ($item_list as $name => $count) $item_accum[] = __($name) . " ({$count})";

                $player->log()->add(new Model_Log_Types_Text('Erfolgreiche Flucht!', 'Du bist entkommen!', 'Schreiend und mit geschlossenen Augen rennst du auf die Zombies zu. Die sind von dieser Aktion so überrascht, dass du die meisten von ihnen einfach aus dem Weg stoßen kannst. ' . ((empty($item_accum)) ? '' : ('<br /><br />Die Zombies, die du nicht einfach wegstoßen kannst lenkst du durch den geschickten Einsatz folgender Gegenstände ab:<br />:items<br /><br />')) . 'Als du deine Augen wieder öffnest, stellst du fest, dass keine Zombies mehr in deiner Nähe sind.', array(':items' => implode(', ', $item_accum))));
				
				$player->achievements()->achieve(Model_Achievement::MA_CLOSE_ESCAPES);
				return true;
			} else $player->log()->add(new Model_Log_Types_Text('Fehlgeschlagene Flucht!', 'Der Kampf beginnt!', 'Schreiend und mit geschlossenen Augen rennst du auf die Zombies zu. Die sind von dieser Aktion so überrascht, dass du die meisten von ihnen einfach aus dem Weg stoßen kannst - aber leider nicht alle. Ein Zombie steht dir mitten im Weg, und wirft dich zu Boden als du versuchst, ihn umzurennen. Zwar kannst du schnell wieder aufspringen, bist nun aber von geifernden Zombies umzingelt. Flucht ist keine Option mehr, du wirst kämpfen müssen.'));
		}

        $zombies = $this->zombie_factory->release();
        $zc = 0;
        foreach ($zombies as $zombie) $zc += $zombie->count();
        $battle = Tool_Scripts::combat([Tool_Scripts::at_location($this->uin()), $zombies], true, 10, $this, 'Du greifst die Zombies an, die den Weg versperren!');
        $this->zombie_factory()->accumulation($battle->count_group_members(2));

		return true;	
	}
	
	public function pretick() {
        /** @global Model_Game $game */
        global $game;

        //Check for zombie attack
        if ($ghuls = $game->get_ghuls($this->uin())) {

            Tool_Scripts::combat([Tool_Scripts::at_location($this->uin()), $ghuls], false, 15, $this, 'Einer deiner zombifizierten Freunde greift an!');

            $battle_won = true;
            if ($battle_won)
                foreach ($ghuls as $key => $data) {
                    $game->unregister_ghul($key);
                    $drop = array();
                    foreach ($data->inventory()->get() as $item) {
                        $drop[] = $item;
                        $this->inventory()->add($item);
                    }

                    $this->log()->add(new Model_Log_Types_Item(Model_Log_Types_Item::MLTI_GHULKILL, $drop, $data->zombiefied_player_id()));
                }

        } else {
            $zombies = $this->zombie_factory()->spawn();
            if ($zombies) Tool_Scripts::combat([Tool_Scripts::at_location($this->uin()), $zombies], true, 20, $this, 'Zombies greifen an!');
        }
	}

	public function tick() {

		$this->find_item();
        $this->find_building();

		return true;
	}

	public function zombie_pop($reset = false) {
		if ($reset) $this->zombie_factory->accumulation(0);
		else return $this->zombie_factory->accumulation();
        return true;
	}

	//Return description
	public function description() {
		return static::$description;
	}

	//Interact with location
	public function interact($action, $argument, $force = false) {
		$method = $force ? "interaction_forced_{$action}" : "interaction_{$action}";
		if (method_exists($this, $method)) $r = $this->$method($argument);
		else throw new Exception("Location method '{$action}' (" . ($force ? 'enforced' : 'unenforced') . ") doesnt exist!", 1);

		return $r;
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

        $this->log->add(new Model_Log_Types_Building($building, $player->id()));
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