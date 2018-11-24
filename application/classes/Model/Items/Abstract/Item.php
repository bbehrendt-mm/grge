<?php defined('SYSPATH') OR die('No direct access allowed.');

abstract class Model_Items_Abstract_Item extends Model_Cloudshard {

	public const MIAI_CAT_GEAR = 1;
	public const MIAI_CAT_FOOD = 2;
	public const MIAI_CAT_DRUG = 4;
	public const MIAI_CAT_FIGHT = 8;
	public const MIAI_CAT_RES = 16;
	public const MIAI_CAT_MISC = 32;
    public const MIAI_CAT_EVENT = 64;
    public const MIAI_CAT_LITERATURE = 128;
    public const MIAI_CAT_BOTTLES = 256;

	public static function translateCatID($gid): string {
		switch ($gid) {
			case self::MIAI_CAT_GEAR:          return 'Ausrüstung';
			case self::MIAI_CAT_FIGHT:         return 'Waffen und Verteidigung';
			case self::MIAI_CAT_FOOD:          return 'Nahrungsmittel';
			case self::MIAI_CAT_DRUG:          return 'Drogen und med. Zubehör';
			case self::MIAI_CAT_RES:           return 'Baumaterialien';
			case self::MIAI_CAT_EVENT:         return 'Besonderes';
			case self::MIAI_CAT_LITERATURE:    return 'Lesestoff';
			case self::MIAI_CAT_BOTTLES:       return 'Wasserbehälter';
			case self::MIAI_CAT_MISC: default: return 'Sonstiges';
		}
	}

	protected static $static_info = Array(
				'name' => 'item_name',
				'icon' => 'icon_name',
				'description' => 'item_desc',
				'category' => 'item_cat',
                'deco' => 0,
			);
	protected static $instances_info = Array();
    protected static $carrier_item = false;
    protected static $max_per_player = 0;
	protected $custom_info = Array();
    /** @var string|null $idea_contest_player */
	protected static $idea_contest_player;
	
	public $type = -1;

	/** @var float $weight */
	protected static $weight = 0.0;
	protected static $essential = false;

    public function is_carrier_item(): bool {
        return static::$carrier_item;
    }

    public function get_max_per_player(): int {
        return static::$max_per_player;
    }

    public static function idea_contest_player(): ?string {
        return static::$idea_contest_player;
    }

    /**
     * @return Model_Hid
     * @throws Exception
     */
    protected function hid(): Model_Hid {
        $hid = Model_Hid::factory($this, static::class);

        if (Globals::hasCurrentGame())
            foreach (Globals::CurrentGameF()->get_initialized_events() as $ev)
                $ev->event_generateHIDStack($this, $hid);
        return $hid;



    }

    /**
     * Item constructor
     * Will randomly select a subtype if subtypes are defined for this item class
     *
     * @param null $type
     *
     * @throws Exception
     */
	public function __construct($type = null) {
		if (count(static::$instances_info) > 0)
			$this->type = ($type === null || $type < 0 || $type > (count(static::$instances_info) - 1)) ? random_int(0, count(static::$instances_info) - 1) : $type;
	}

	public static function getNumberOfTypes(): int
    {
		return count(static::$instances_info);
	}

    /**
     * Returns the number of static variants of this item
     * @return int
     */
    public function variants(): int
    {
        return count(static::$instances_info);
    }
	
	/**
	 * Will return static infos about this class (disregarding subtypes)
	 * @param string $name The type of information (name, icon, description, category)
	 * @return string
	 */
	public static function static_info($name): string
    {
		return static::$static_info[$name] ?? null;
	}

    /**
     * Will return static infos about this class
     *
     * @param string $name The type of information (name, icon, description, category)
     * @param int    $type
     *
     * @return string
     */
    public static function static_typed_info($name, $type = 0): string {
        //1.Lv: Instance info
        if (isset(static::$instances_info[$type][$name])) return static::$instances_info[$type][$name];
        //2.Lv: Static info
        else return static::static_info($name);
    }

	/**
	 * Will return subtype infos about this class instance
	 * If no subtypes are defined for this class, this acts as a non-static alias for static_info
	 * @param string $name The type of information (name, icon, description, category)
	 * @return string
	 */
	private function instance_info($name): string {
		//1.Lv: Custom Info
		if (isset($this->custom_info[$name])) return $this->custom_info[$name];
		//2.Lv: Static info (if subtype is set)
		elseif ($this->type < 0) return static::static_info($name);
		//3.Lv: Instance info
		elseif (isset(static::$instances_info[$this->type][$name])) return static::$instances_info[$this->type][$name];
		//4.Lv: Static info
		else return static::static_info($name);
	}
	
	/**
	 * Will return the item instance category
	 * @return int
	 */
	public function cat(): int
    {
		return $this->instance_info('category');
	}

    /**
     * Will return the item static category
     *
     * @param null $type
     *
     * @return int
     */
	public static function static_cat($type = null): int
    {
		return $type === null ? static::static_info('category') : static::static_typed_info('category', $type);
	}

    /**
     * Will return item instance name
     *
     * @return string
     */
	public function name(): string {
		return $this->instance_info('name');
	}

    /**
     * Will return item static name
     *
     * @param null $type
     *
     * @return string
     */
	public static function static_name($type = null): string
    {
        return $type === null ? static::static_info('name') : static::static_typed_info('name', $type);
	}

    /**
     * Will return item instance icon path
     *
     * @return string
     */
	public function icon(): string {
		return 'items/' . $this->instance_info('icon');
	}

    /**
     * Will return item static icon path
     *
     * @param null $type
     *
     * @return string
     */
	public static function static_icon($type = null): string
    {
	    $tmp = $type === null ? static::static_info('icon') : static::static_typed_info('icon', $type);
	    return $tmp ? "items/$tmp" : '';
	}

    /**
     * Will return item instance description
     *
     * @return string
     */
	public function description(): string {
		return $this->instance_info('description');
	}

    /**
     * Will return item static description
     *
     * @param null $type
     *
     * @return string
     */
	public static function static_description($type = null): string
    {
        return $type === null ? static::static_info('description') : static::static_typed_info('description', $type);
	}

    /**
     * Will return item deco value
     * @return int
     */
    public function deco(): int
    {
        return $this->instance_info('deco');
    }

    /**
     * Will return item static deco value
     *
     * @param null $type
     *
     * @return int
     */
    public static function static_deco($type = null): int
    {
        return $type === null ? static::static_info('deco') : static::static_typed_info('deco', $type);
    }

	/**
	 * Will return weigth of this item
	 * @return int
	 */
	public function weight(): int
    {
		return static::$weight;
	}
	
	/**
	 * Returns true if this is an essential item
	 * @return boolean
	 */
	public function is_essential(): bool
    {
		return static::$essential;
	}


    /**
     * Returns weather this item can be taken by a player
     *
     * @param bool $silent Set true to suppress notifications
     *
     * @return bool True, when the item can be taken
     */
    public function take($silent = false): bool {
		return true;
	}

    /**
     * Returns weather this item can be dropped by a player
     *
     * @param Interface_Plentity|null $p
     * @param bool                    $silent Set true to suppress notifications
     *
     * @return bool True, when the item can be dropped
     */
	public function drop($p = null, $silent = false): bool {
		return true;
	}

    /**
     * Returns the autoaction-list
     *
     * @param Interface_Plentity[]|null $players
     *
     * @return array
     * @throws Exception
     */
    public function auto_actions($players = null): array
    {
        return $this->hid()->convert($this->uin(), $players);
    }

    /**
     * Destroys the item; this function can be overridden by an upstream class to incorporate additional effects or replace the destruction completely
     *
     * @return int The item count, or 1 if this item is not countable
     * @throws Exception
     */
    public function consume(): int {
		if ($this->obj_uin) Globals::CurrentGameF()->uin()->remove($this->obj_uin);
		return $this->count() ?? 1;
	}

    /**
     * Destroys the item; this function may not be overridden as it exists to make sure there is a method to completely destroy an item without regard of the items state
     */
    public function grind(): void
    {
		if ($this->obj_uin) Globals::CurrentGameF()->uin()->remove($this->obj_uin);
	}

    /**
     * Runs any of the items interaction_ functions
     *
     * @param string             $action   Action to execute
     * @param Interface_Plentity $player
     * @param null|mixed         $argument Optional argument
     * @param null|Model_Player  $side_player
     *
     * @return mixed Return value of the called interaction function
     * @throws Exception
     */
    public function interact($action, $player, $argument = NULL, $side_player = null) {
        $hid = $this->hid();
        $player->get_status()->set_cause_of_death('Vergiftung');
        if ($hid->can($action))
            return $hid->perform($action, $player, $side_player, $argument);
        $player->get_status()->clear_cause_of_death();
        return false;
	}

    /**
     * Runs the description of an action ID
     *
     * @param string             $action Action
     * @param Interface_Plentity $player
     *
     * @return mixed Action description or null, if ID is invalid
     * @throws Exception
     */
    public function resolve_action($action, $player) {
        $hid = $this->hid();

        if ($hid->can($action))
            return $hid->actions()[$action];
        else return null;
    }

    public function simple_effects($p = null, $auto = false): array {
        return $this->hid()->simple_effects($p, $auto);
    }

    /**
     * Runs any of the items interaction_ functions
     *
     * @param string             $action   Action to execute
     * @param Interface_Plentity $player
     * @param null|mixed         $argument Optional argument
     * @param null|Model_Player  $side_player
     *
     * @return mixed Return value of the called interaction function
     * @throws Exception
     */
    public function test_interaction($action, $player, $argument = NULL, $side_player = null) {
        $hid = $this->hid();
        return $hid->can($action) && $hid->test($action, $player, $side_player, $argument);
    }

    /**
     * This function is called internally when the item is mixed with a chemical substance
     *
     * @param number $chemval Value of the chemical substance
     *
     * @return bool True, to award the positive chem achievement, false to award the negative one
     * @throws Exception
     */
    public function mixchem($chemval): bool
    {
        Globals::PrimaryPlayerF()->log()->add(new Model_Log_Types_String(null, 'Du schüttest die Chemikalie über diesem Gegenstand aus. Es riecht ein wenig komisch, aber sonst geschieht nichts... Schade.'));
		return false;
	}
	
	/**
	 * Returns an item to be used to drop to ground, when a player holding this dies (can also return null or an array)
	 * @return Model_Items_Abstract_Item|Model_Items_Abstract_Item[]|null
	 */
	public function drop_dead() {
		return $this;
	}

    /**
     * Returns the item count, or null when the item is not countable
     * @return null|number
     */
    public function count() {
		return null;
	}

    /**
     * Returns the item capacity, or null when this item has no capacity
     * @return null|number
     */
    public function capacity() {
		return null;
	}

    /**
     * Returns the item stack name, or null when the item has no stack name
     *
     * @return null|string
     */
    public function stackname(): ?string {
		return null;
	}

    /**
     * Returns the item label, or null when the item has no label
     *
     * @return null|string
     */
    public function label(): ?string {
		return null;
	}
}	