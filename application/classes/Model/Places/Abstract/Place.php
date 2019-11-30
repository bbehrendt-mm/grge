<?php defined('SYSPATH') OR die('No direct access allowed.');

abstract class Model_Places_Abstract_Place extends Model_Cloudshard {
	
	protected static $location_name;
    protected static $icon = 'default';
	protected static $namelist;
	/** @var string|null $description */
	protected static $description;
    protected static $outside = true;

    protected static $custom_style;
    protected static $upgradable = true;
    protected static $defendable = true;
    protected static $perpetualDaytime;

    protected static $user_rooms_allowed = 0;
    protected static $user_rooms_size = 10;
    protected $user_room_count = 0;
	
	protected static $weight_limit;

	protected $variant_name;
	
	protected $inventory;

    //protected $upgrades = [];
    /** @var Model_Room[]  */
    protected $rooms = [];

    /** @var Model_Factory_Zombies  */
	protected $zombie_factory;
    /** @var  Model_Factory_Items */
	protected $item_factory;
	
	protected $log;

    protected $doorway = array();
    protected static $auto_doorways = array();

    protected $temperature_buildup = 0.0;

    protected static $temperature_engine = null;
    protected $temperature_scale     = 0.05;
    protected $temperature_deisolation = 0.15;

    public function get_base_temperature(): ?float {
        $b = static::$temperature_engine;
        if ($b === null) return $b;
        else return $b + (Globals::CurrentGameF()->duration() / 288) * Globals::CurrentGameF()->config('game.bhav.daily_temperature_change');
    }



    public function get_temperature(): ?float {
        $t = $this->get_base_temperature();
        if ($t === null) return null;

        $tod = Tool_Scripts::get_timeofday();
        if ($this->is_outside() && $tod === 'day')   $t += 10;
        if ($this->is_outside() && $tod === 'night') $t -= 10;

        if (!$this->is_outside() && $tod === 'day')   $t += 3;
        if (!$this->is_outside() && $tod === 'night') $t -= 3;

        return $t + $this->temperature_buildup;
    }

	public function is_upgradable(): bool
    {
	    return static::$upgradable;
    }

    public function is_defendable(): bool
    {
        return static::$defendable;
    }

    public static function get_namelist(): array
    {
        return static::$namelist ?: [static::$location_name];
    }

    public static function get_icon(): string {
	    return static::$icon;
    }

    public static function getCustomStyle() {
        return static::$custom_style;
    }

    public function is_outside(): bool
    {
        return static::$outside;
    }

    public function battle_location_type(): string
    {
        return $this->is_outside() ? 'outside' : 'inside';
    }

    public function get_doorways(): array
    {
        $ret = [];
        foreach ($this->doorway as $dw)
            if (Globals::CurrentGameF()->location($dw))
                $ret[] = $dw;
        return $ret;
    }

    public function register_doorway($lid): void
    {
        $this->doorway[] = $lid;
    }

    public function getPerpetualDayTime() {
        return static::$perpetualDaytime;
    }

    public function trigger_item_action(?Model_Items_Abstract_Item $item) {}

    /**
     * @param int $id
     * @return Model_Room|null
     */
    public function room($id = 0) : ?Model_Room {
	    if ($id < 0 || $id >= count($this->rooms)) return null;
	    else return $this->rooms[$id];
    }

    /**
     * @param int $id
     * @return Model_Room
     */
    public function roomF($id = 0) : Model_Room {
        $r = $this->room($id);
        if ($r === null) throw new RuntimeException('Attempt to fetch non-existent room.');
        return $r;
    }

    /**
     * @param string|null $chk
     * @return string[]|bool
     */
    public function rooms_contain($chk = null) {
        $accum = [];
        $res = false;
        foreach ($this->rooms as $room)
            if ($chk === null) $accum = array_merge($accum, $room->get_content(), $room->get_satisfaction());
            else $res = $res || $room->check_room_satisfaction($chk);
        return ($chk === null) ? array_unique($accum) : $res;
    }

    /**
     * @return Model_Room[]
     */
    public function rooms(): array
    {
        return $this->rooms;
    }

    /**
     * @param string|string[] $room_type
     * @param string|string[] $contains
     * @param string|string[] $tags
     * @return bool
     */
    public function has_room($room_type = '', $contains = '', $tags = ''): bool
    {
        return count($this->find_rooms($room_type,$contains,$tags)) > 0;
    }

    /**
     * @param string|string[] $room_type
     * @param string|string[] $contains
     * @param string|string[] $tags
     * @return Model_Room[]
     */
    public function find_rooms($room_type = '', $contains = '', $tags = ''): array
    {
        $ret = [];
        foreach ($this->rooms() as $room)
            if ($room->check_room_satisfaction($room_type) && $room->has_content($contains) && $room->has_tag($tags))
                $ret[] = $room;
        return $ret;
    }

    public function uin($uin = NULL) {
        if ($uin === NULL) return parent::uin();
        else $t = parent::uin($uin);

        //Register sub locations
        foreach (static::$auto_doorways as $dwid) {
            $slid = Globals::CurrentGameF()->register_map("submap_{$dwid}_{$uin}", $dwid);
            if ($slid) {
                $this->register_doorway($slid);
                Globals::CurrentGameF()->locationF($slid)->register_doorway($uin);
            }
        }

        $this->inventory->add(new Model_Items_Virtual_Location_Place());

        foreach (Globals::CurrentGameF()->get_initialized_events() as $ev)
            $ev->event_locationCreation($this);

        return $t;
    }

    public function create_new_room($space = -1, $tags = []): Model_Room
    {
        return $this->rooms[] = Model_Room::factory(count($this->rooms),$space,$tags);
    }

    public function setup_new_room(Model_Room $room, $rtype = [], $upgrades = [], $name = null): Model_Room
    {
        Model_Blueprints::fast_apply($this, 'rooms', $rtype, $room);
        Model_Blueprints::fast_apply($this, 'upgrades', $upgrades, $room);
        if ($name !== null) $room->name($name);
        return $room;
    }

    /**
     * @return Model_Room
     */
    public function setup_primary_rooms(): Model_Room
    {
        $room = $this->create_new_room(-1,['inside','primary']);
        $room->name($this->name(), true);
        $room->upgrade('Allgemein',true, ['common']);
        $room->name_is_fixed(true);
        return $room;
    }

    public function setup_additional_rooms(): void
    {}

    public function user_room_available(): bool {
        return $this->user_room_count < static::$user_rooms_allowed;
    }

    public function setup_user_room(): ?Model_Room
    {
        $success = $this->user_room_available();
        if ($success) {
            $room = $this->create_new_room(static::$user_rooms_size,[]);
            $this->user_room_count++;
            return $room;
        }
        return null;
    }

    public function mapable(): bool
    {
        return true;
    }

    protected static $survival_find_available = true;
	protected $survival_find ;
	
	//Create a new inventory and assign a variable name (if a name list is present from which to choose)
	public function __construct() {
		$this->inventory = new Model_Inventory;

		$this->log = new Model_Log_Log();
		/** @var Model_Factory_Zombies zombie_factory */
        $this->zombie_factory = Model_Factory_Zombies::read(static::class, Globals::CurrentGameF()->config('game.config.spawn'));
        /** @var Model_Factory_Items item_factory */
        $this->item_factory = Model_Factory_Items::read(static::class, Globals::CurrentGameF()->config('game.config.itemset'));
		$this->item_factory->modify_decay(Globals::CurrentGameF()->config('places.dryout_factor'));

		if (static::$namelist) {
            $list = array();
            $c = count(static::$namelist);
            for ($i = 0; $i < $c; $i++)
                if (Globals::CurrentGameF()->ndp_check(static::class, $i))
                    $list[] = $i;

            if (!$list) {
                Globals::CurrentGameF()->ndp_purge(static::class);
                $type = random_int(0, count(static::$namelist) - 1);
            } else $type = $list[random_int(0, count($list) - 1)];

            $this->variant_name = static::$namelist[$type];
            Globals::CurrentGameF()->ndp_register(static::class, $type);
        }

        $this->survival_find = !static::$survival_find_available;

        $this->setup_primary_rooms();
        $this->setup_additional_rooms();
	}
	
	/**
	 * Returns local zombie factory
	 * @return Model_Factory_Zombies
	 */
	public function zombie_factory(): \Model_Factory_Zombies
    {
		return $this->zombie_factory;
	}

    public function item_factory(): \Model_Factory_Items
    {
        return $this->item_factory;
    }

    public function log(): \Model_Log_Log
    {
		return $this->log;
	}
	
	public function weight_limit() {
		return static::$weight_limit;
	}

	public function inventory(): \Model_Inventory
    {
		return $this->inventory;
	}
	
	//Enter location
	public function can_enter($pid = null, $type = Interface_Tickable::IT_TYPE_PLAYER): bool
    {
		return true;
	}
	
	//Leave location
	public function can_leave($pid = null, $ignore_zombies = false, $type = Interface_Tickable::IT_TYPE_PLAYER): bool
    {
        if (!($p = Globals::CurrentGameF()->get_player($pid))) return false;
        return ($ignore_zombies || ($type === Interface_Tickable::IT_TYPE_PLAYER && $p->can_escape()) || $this->zombie_factory->accumulation() <= 0);
	}

	public function pre_render(): void {}

    //Enter map
    public function can_enter_map($pid = null, $type = Interface_Tickable::IT_TYPE_PLAYER): bool
    {
        return $this->can_enter($pid, $type);
    }

    //Leave map
    public function can_leave_map($pid = null, $type = Interface_Tickable::IT_TYPE_PLAYER): bool
    {
        return $this->can_leave($pid, $type);
    }
	
	//Enter location
	public function enter($pid = null, $type = Interface_Tickable::IT_TYPE_PLAYER): bool
    {
		if (!$pid) $player = Globals::CurrentPlayerF();
		elseif ($type === Interface_Tickable::IT_TYPE_PLAYER) $player = Globals::CurrentGameF()->get_player($pid);
        else $player = Globals::CurrentGameF()->get_npc($pid);

		$this->log->add(new Model_Log_Types_Movement(Model_Log_Types_Movement::MOVEMENT_TYPE_ENTER, $pid, $type === Interface_Tickable::IT_TYPE_NPC));

        if (!$this->survival_find && $type === Interface_Tickable::IT_TYPE_PLAYER && ($player->job(1060) || $player->job(1061))) {
			
			$this->survival_find = true;
			$findings = $player->job(1060) ? min(2,max(0,$player->job(false) - 2)) : 5;
			
			if ($findings > 0) {
				$items = [];
				for ($i = 0; $i < $findings; $i++)
                    if ($find = $this->item_factory->nd_spawn())
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
	public function leave($pid = null, $type = Interface_Tickable::IT_TYPE_PLAYER): bool
    {
		$this->log->add(new Model_Log_Types_Movement(Model_Log_Types_Movement::MOVEMENT_TYPE_LEAVE, $pid, $type === Interface_Tickable::IT_TYPE_NPC));
		if (count(Tool_Scripts::at_location($this->uin(), true, true)) <= 1) $this->vacate();
		return true;
	}

    //Enter map
    public function enter_map($pid = null, $type = Interface_Tickable::IT_TYPE_PLAYER): bool
    {
        return $this->enter($pid, $type);
    }

    //Leave map
    public function leave_map($pid = null, $type = Interface_Tickable::IT_TYPE_PLAYER): bool
    {
        return $this->leave($pid, $type);
    }

    public function pass($pid = null, $type = Interface_Tickable::IT_TYPE_PLAYER): bool
    {
        $this->log->add(new Model_Log_Types_Movement(Model_Log_Types_Movement::MOVEMENT_TYPE_PASS, $pid, $type === Interface_Tickable::IT_TYPE_NPC));

        return true;
    }
	
	public function vacate(): void
    {
		foreach ($this->inventory->get('Interface_Tmpitem') as $item) $item->consume();
		$this->log->trim(5);

		if (!Tool_System::instance_of($this,'Interface_Pretickable'))
		    $this->temperature_buildup = 0;
	}
	
	//Return name
	public function name() {
		return $this->variant_name ?: static::$location_name;
	}

    /**
     * Will return item icon path
     * @return string
     */
    public function icon(): string
    {
        return static::$icon . '.gif';
    }

    /**
     * @param bool $force
     * @param bool $return
     * @return bool|Model_Items_Abstract_Item|null
     * @throws Exception
     */
	public function find_item($force = false, $return = false) {
        // Spawn ticket
        /** @noinspection NotOptimalIfConditionsInspection */
        if (!$return
            && !Tool_Scripts::is_npc(Globals::CurrentPlayerF()) && Tool_Events::ticket_event(Globals::CurrentGameF()->next_tick())
            && !Globals::CurrentPlayerActualF()->golden_ticket()
        ) {
            $num = max(1,random_int(1,3) - random_int(0,2));
            $tmp = array();
            for ($i = 0; $i < $num; $i++)
                $tmp[] = new Model_Items_Generic_Ticket();

            Tool_Scripts::place_new_item($tmp);
            Globals::CurrentPlayerActualF()->golden_ticket(true);
        }

        // Spawn BrainCoins
        if (!$return && Tool_Gambling::random(Tool_Scripts::getBrainCoinLikelinessLevel($this->uin())))
            Tool_Scripts::place_new_item(new Model_Items_Braincoin());


		if (!$return && (Globals::CurrentPlayerF()->get_status()->retrieve('fragile') || Globals::CurrentPlayerF()->get_status()->retrieve('passout'))) return true;
		$item = $this->item_factory->spawn($force, true, Tool_Scripts::calculate_find_chances(Globals::CurrentPlayerF()->id()));

        if ($item !== null && Tool_System::instance_of($item, Model_Items_Virtual_Invoke_Abstract::cls())) {
            /** @var $item Model_Items_Virtual_Invoke_Abstract */
            $item->trigger_spawn($this, Globals::CurrentPlayerF());
            $item->grind();
            $item = null;
        }

        if ($item && !$return) {
            Tool_Scripts::place_new_item($item);
            foreach (Globals::CurrentGameF()->get_initialized_events() as $ev)
                $ev->event_findItem($this, $item);
            return true;
        }
        elseif ($item && $return) return $item;
        elseif (!$item && $return) return null;
        else return true;
	}

    public function hero_replensish($val = 0.75): void {
        $this->item_factory->replenish($val);
    }

    /**
     * @param Model_Combat_Actor[]|null $data
     *
     * @return Model_Combat_Actor[]
     */
    public function modify_spawned_zombies(?array $data): array {
	    return $data ?: [];
    }

	public function break_out($fight): bool {
		if (!$fight) {
			//Attempt to flee
			$c = Globals::CurrentGameF()->config('zombies.escape_threshold');
			for ($i = 0; $i < $this->zombie_factory->accumulation(); $i++) $c += random_int(0, ceil($this->zombie_factory->accumulation()/5));
			
			$c = ceil($c * (1 + (Globals::CurrentPlayerF()->get_status()->get(Model_Status::MS_STAT_DRUNK) / 100)));
			
			$item_list = Array();
			while (((Globals::CurrentPlayerF()->get_status()->get(Model_Status::MS_STAT_ENERGY) * Globals::CurrentPlayerF()->get_status()->get(Model_Status::MS_CHAR_EVASIVENESS)) < $c) && ($items = Tool_Scripts::get_items(Model_Items_Abstract_Escape::cls())))
			{
				/** @var $items Model_Items_Abstract_Escape[] */
                $c -= $items[0]->escape();
				if (!isset($item_list[$items[0]->name()])) $item_list[$items[0]->name()] = 1;
				else $item_list[$items[0]->name()]++;
				$items[0]->consume();
			}

            Globals::CurrentPlayerF()->get_status()->modify(Model_Status::MS_STAT_ENERGY, -10);
			if ((Globals::CurrentPlayerF()->get_status()->get(Model_Status::MS_STAT_ENERGY) * Globals::CurrentPlayerF()->get_status()->get(Model_Status::MS_CHAR_EVASIVENESS)) >= $c) {
				$c = $this->zombie_pop();
                $dec = $c - ceil($c/(1.05 * Globals::CurrentPlayerF()->get_status()->get(Model_Status::MS_CHAR_BULKYNESS)));
				$this->zombie_factory()->reduce_accum($dec);

                Globals::CurrentPlayerF()->enable_escape();
				
				$item_accum = Array();
				if (count($item_list) > 0)
					foreach ($item_list as $name => $count) $item_accum[] = __($name) . " ({$count})";

                Globals::CurrentPlayerActualF()->log()->add(new Model_Log_Types_String('Erfolgreiche Flucht!', 'Schreiend und mit geschlossenen Augen rennst du auf die Zombies zu. Die sind von dieser Aktion so überrascht, dass du die meisten von ihnen einfach aus dem Weg stoßen kannst. ' . (empty($item_accum)
                        ? '' : '<br /><br />Die Zombies, die du nicht einfach wegstoßen kannst lenkst du durch den geschickten Einsatz folgender Gegenstände ab:<br />:items<br /><br />') . 'Als du deine Augen wieder öffnest, stellst du fest, dass keine Zombies mehr in deiner Nähe sind.', array(':items' => implode(', ', $item_accum))));
                Globals::CurrentPlayerActualF()->achievements()->achieve(Model_Achievement::MA_CLOSE_ESCAPES);
				return true;
			} else Globals::CurrentPlayerActualF()->log()->add(new Model_Log_Types_String('Fehlgeschlagene Flucht!', 'Schreiend und mit geschlossenen Augen rennst du auf die Zombies zu. Die sind von dieser Aktion so überrascht, dass du die meisten von ihnen einfach aus dem Weg stoßen kannst - aber leider nicht alle. Ein Zombie steht dir mitten im Weg, und wirft dich zu Boden als du versuchst, ihn umzurennen. Zwar kannst du schnell wieder aufspringen, bist nun aber von geifernden Zombies umzingelt. Flucht ist keine Option mehr, du wirst kämpfen müssen.'));
		}

        $zombies =  $this->modify_spawned_zombies( $this->zombie_factory->release() );
        $zc = 0;
        foreach ($zombies as $zombie) $zc += $zombie->count();
        $battle = Tool_Scripts::combat([Tool_Scripts::at_location($this->uin()), $zombies], false, 10, $this, 'Du greifst die Zombies an, die den Weg versperren!');
        $this->zombie_factory()->accumulation($battle->count_group_members(2));

		return true;	
	}

	public function passive_pretick(): void {
        // Temperature
        $bt = $this->get_base_temperature(); $t0 = $this->get_temperature();
        if (!$this->is_outside() && $bt !== null) {

            foreach ($this->inventory()->get(Model_Items_Generic_Fire::cls()) as $f)
                $this->temperature_buildup += $this->temperature_scale * 2 * max(0,( 30 - max(0,$t0) ));
            foreach ($this->inventory()->get(Model_Items_Generic_Fire2::cls()) as $f)
                $this->temperature_buildup += $this->temperature_scale * 2.5 * max(0,( 50 - max(0,$t0) ));

            foreach (Tool_Scripts::at_location($this->uin(),true,true) as $p)
                if ($buff = $p->get_status()->retrieve('temperature')) {
                    /** @var Model_Buffs_Temperature $buff */
                    if ($buff->get_temperature_gain() > $t0)
                        $this->temperature_buildup += $this->temperature_scale * ( $buff->get_temperature_gain() - max(0,$t0) );
                }

            $t = $this->get_temperature();
            $this->temperature_buildup -= max(0,$t - $bt) * $this->temperature_deisolation;
        }
    }

	public function pretick(): void
    {
        //Check for zombie attack
        if ($ghuls = Globals::CurrentGameF()->get_ghuls($this->uin())) {

            Tool_Scripts::combat([Tool_Scripts::at_location($this->uin()), $ghuls], false, 15, $this, 'Einer deiner zombifizierten Freunde greift an!');

            $battle_won = true;
            if ($battle_won)
                foreach ($ghuls as $key => $data) {
                    Globals::CurrentGameF()->unregister_ghul($key);
                    $drop = array();
                    foreach ($data->inventory()->get() as $item) {
                        $drop[] = $item;
                        $this->inventory()->add($item);
                    }

                    $this->log()->add(new Model_Log_Types_Item(Model_Log_Types_Item::MLTI_GHULKILL, $drop, $data->zombiefied_player_id()));
                }

        } else {
            $zombies = $this->modify_spawned_zombies($this->zombie_factory()->spawn());
            if ($zombies) Tool_Scripts::combat([Tool_Scripts::at_location($this->uin()), $zombies], true, 20, $this, 'Zombies greifen an!');
        }

        foreach (Globals::CurrentGameF()->get_initialized_events() as $ev)
            $ev->event_locationTick($this);
	}

	public function tick($type = Interface_Tickable::IT_TYPE_PLAYER): bool
    {
        if (Globals::CurrentPlayerF()->can(Interface_Plentity::IC_TRIGGER_ITEM_FINDINGS)) $this->find_item();
        if (Globals::CurrentPlayerF()->can(Interface_Plentity::IC_TRIGGER_LOCATION_FINDINGS)) $this->find_building();

		return true;
	}

	public function zombie_pop($reset = false) {
		if ($reset) return $this->zombie_factory->accumulation(0);
		else return $this->zombie_factory->accumulation();
	}

	//Return description
	public function description() {
		return static::$description;
	}

	//Interact with location
	public function interact($action, $argument, $force = false) {
		$method = $force ? "interaction_forced_{$action}" : "interaction_{$action}";
		if (method_exists($this, $method)) $r = $this->$method($argument);
		else throw new LogicException("Location method '{$action}' (" . ($force ? 'enforced' : 'unenforced') . ') doesnt exist!', 1);

		return $r;
	}

    protected function find_building(): bool
    {
        if (Globals::CurrentPlayerF()->get_status()->retrieve('fragile')) return false;
        if (!($building = Globals::CurrentGameF()->mapF($this->uin())->attempt_unveil($this->uin(), Globals::CurrentPlayerF()->get_status()->get(Model_Status::MS_CHAR_LOCATION_SPAWNRATE)))) return true;

        //Mapper
        if (Globals::CurrentGameF()->config('modules.mapping') && Globals::CurrentGameF()->main_map()->has_location($this->uin()) && ($items = Globals::CurrentPlayerF()->inventory()->get(Model_Items_Maptool::cls()))) {
            /** @var $items Model_Items_Maptool[] */
            if (!$items) return false;
            $items[0]->common_discovery(random_int(5, 15));
            Globals::PrimaryPlayerF()->log()->add(new Model_Log_Types_String(null, 'Du hast eine neue Ruine entdeckt und eine grobe Karte mit ihrer Position gezeichnet. Diese Informationen sind sicher nützlich für deine Stadt.... besser wäre es natürlich, du würdest diese Ruine genauer erkunden.'));
        }

        $this->log->add(new Model_Log_Types_Building($building, Globals::CurrentPlayerF()->id()));
        return true;
    }

    public function grind(): void {
        $this->inventory()->grind();
        Globals::CurrentGameF()->uin()->remove($this->uin());
    }
}	