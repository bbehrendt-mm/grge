<?php defined('SYSPATH') OR die('No direct access allowed.');

abstract class Model_Items_Abstract_Item extends Model_Cloudshard {

	const MIAI_CAT_GEAR = 1;
	const MIAI_CAT_FOOD = 2;
	const MIAI_CAT_DRUG = 4;
	const MIAI_CAT_FIGHT = 8;
	const MIAI_CAT_RES = 16;
	const MIAI_CAT_MISC = 32;
    const MIAI_CAT_EVENT = 64;
    const MIAI_CAT_LITERATURE = 128;
    const MIAI_CAT_BOTTLES = 256;

	public static function translateCatID($gid) {
		switch ($gid) {
			case Model_Items_Abstract_Item::MIAI_CAT_GEAR:          return 'Ausrüstung';
			case Model_Items_Abstract_Item::MIAI_CAT_FIGHT:         return 'Waffen und Verteidigung';
			case Model_Items_Abstract_Item::MIAI_CAT_FOOD:          return 'Nahrungsmittel';
			case Model_Items_Abstract_Item::MIAI_CAT_DRUG:          return 'Drogen und med. Zubehör';
			case Model_Items_Abstract_Item::MIAI_CAT_RES:           return 'Baumaterialien';
			case Model_Items_Abstract_Item::MIAI_CAT_EVENT:         return 'Besonderes';
			case Model_Items_Abstract_Item::MIAI_CAT_LITERATURE:    return 'Lesestoff';
			case Model_Items_Abstract_Item::MIAI_CAT_BOTTLES:       return 'Wasserbehälter';
			case Model_Items_Abstract_Item::MIAI_CAT_MISC: default: return 'Sonstiges';
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
    protected static $idea_contest_player = null;
	
	public $type = -1;
	
	protected static $weight;
	protected static $essential = false;
	
	protected static $associated_view = 'auto';

    public function is_carrier_item() {
        return static::$carrier_item;
    }

    public function get_max_per_player() {
        return static::$max_per_player;
    }

    public static function idea_contest_player() {
        return static::$idea_contest_player;
    }

    /**
     * @return Model_Hid
     */
    protected function hid() {
        return Model_Hid::factory();
    }
	
	/**
	 * Item constructor
	 * Will randomly select a subtype if subtypes are defined for this item class
	 */
	public function __construct($type = null) {
		if (count(static::$instances_info) > 0)
			$this->type = ($type === null || $type < 0 || $type > (count(static::$instances_info) - 1)) ? mt_rand(0, count(static::$instances_info) - 1) : $type;
	}

	public static function getNumberOfTypes() {
		return count(static::$instances_info) - 1;
	}

    /**
     * Returns the number of static variants of this item
     * @return int
     */
    public function variants() {
        return count(static::$instances_info);
    }
	
	/**
	 * Will return static infos about this class (disregarding subtypes)
	 * @param string $name The type of information (name, icon, description, category)
	 * @return string
	 */
	public static function static_info($name) {
		return isset(static::$static_info[$name]) ? static::$static_info[$name] : null;
	}
	
	/**
	 * Will return subtype infos about this class instance
	 * If no subtypes are defined for this class, this acts as a non-static alias for static_info
	 * @param string $name The type of information (name, icon, description, category)
	 * @return string
	 */
	private function instance_info($name) {
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
	public function cat() {
		return $this->instance_info('category');
	}
	
	/**
	 * Will return the item static category
	 * @return int
	 */
	public static function static_cat() {
		return static::static_info('category');
	}
	
	/**
	 * Will return item instance name
	 * @return string
	 */
	public function name() {
		return $this->instance_info('name');
	}
	
	/**
	 * Will return item static name
	 * @return string
	 */
	public static function static_name() {
		return static::static_info('name');
	}
	
	/**
	 * Will return item instance icon path
	 * @return string
	 */
	public function icon() {
		return 'items/' . $this->instance_info('icon');
	}
	
	/**
	 * Will return item static icon path
	 * @return string
	 */
	public static function static_icon() {
		return ($tmp = static::static_info('icon')) ? "items/$tmp" : '';
	}
	
	/**
	 * Will return item instance description
	 * @return string
	 */
	public function description() {
		return $this->instance_info('description');
	}
	
	/**
	 * Will return item static description
	 * @return string
	 */
	public static function static_description() {
		return static::static_info('description');
	}

    /**
     * Will return item deco value
     * @return int
     */
    public function deco() {
        return $this->instance_info('deco');
    }

    /**
     * Will return item static deco value
     * @return int
     */
    public static function static_deco() {
        return static::static_info('deco');
    }

	/**
	 * Will return weigth of this item
	 * @return int
	 */
	public function weight() {
		return static::$weight;
	}
	
	/**
	 * Returns true if this is an essential item
	 * @return boolean
	 */
	public function is_essential() {
		return static::$essential;
	}


    /**
     * Returns weather this item can be taken by a player
     * @param bool $silent Set true to suppress notifications
     * @return bool True, when the item can be taken
     */
    public function take($silent = false) {
		return true;
	}

    /**
     * Returns weather this item can be dropped by a player
	 * @param bool $silent Set true to suppress notifications
     * @return bool True, when the item can be dropped
     */
	public function drop($silent = false) {
		return true;
	}

    /**
     * Returns the autoaction-list
     * @return array
     */
    public function auto_actions() {
        return static::hid()->convert($this->uin());
    }

    /**
     * Destroys the item; this function can be overridden by an upstream class to incorperate additional effects or replace the destruction completely
     */
    public function consume() {
        /**
         * @global $game Model_Game
         */
        global $game;
		if ($this->uin) $game->uin()->remove($this->uin);
	}

    /**
     * Destroys the item; this function may not be overridden as it exists to make sure there is a method to completely destroy an item without regard of the items state
     */
    public function grind() {
        /**
         * @global $game Model_Game
         */
        global $game;
		if ($this->uin) $game->uin()->remove($this->uin);
	}

    /**
     * Runs any of the items interaction_ functions
     * @param string $action Action to execute
     * @param null|mixed $argument Optional argument
     * @param null|Model_Player $side_player
     * @return mixed Return value of the called interaction function
     */
    public function interact($action, $argument = NULL, $side_player = null) {
        /**
         * @global $player Model_Player
         */
        global $player;

        $hid = $this->hid();
        $player->get_status()->set_cause_of_death("Vergiftung");
        if ($hid->can($action)) {
            return $hid->perform($action, $player, $side_player, $argument);
        } else {
            $method = "interaction_{$action}";
            if (method_exists($this, $method)) {
                $r = $this->$method($argument);
                $player->get_status()->clear_cause_of_death();
            } else return false;

            return $r;
        }
	}

    /**
     * This function is called internally when the item is mixed with a chemical substance
     * @param number $chemval Value of the chemical substance
     * @return bool True, to award the positive chem achievement, false to award the negative one
     */
    public function mixchem($chemval) {
        /**
         * @global $player Model_Player
         */
        global $player;

        $player->log()->add(new Model_Log_Types_Text(null, null, 'Du schüttest die Chemikalie über diesem Gegenstand aus. Es riecht ein wenig komisch, aber sonst geschieht nichts... Schade.'));
		return false;
	}
	
	/**
	 * Returns an item to be used to drop to ground, when a player holding this dies (can also return null or an array)
	 * @return Model_Items_Abstract_Item
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
     * @return null|string
     */
    public function stackname() {
		return null;
	}

    /**
     * Returns the item label, or null when the item has no label
     * @return null|string
     */
    public function label() {
		return null;
	}
}	