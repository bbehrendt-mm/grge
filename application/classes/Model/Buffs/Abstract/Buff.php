<?php defined('SYSPATH') OR die('No direct access allowed.');

abstract class Model_Buffs_Abstract_Buff extends Model {

	const MB_RAISE_ACC = 1;
	const MB_RAISE_PRC = 2;
	const MB_DROP_ACC  = 3;
	const MB_DROP_PRC  = 4;

    const MBR_FULLY_RECESSIVE = 1;
    const MBR_PARTIALLY_RECESSIVE = 2;
    const MBR_EQUAL = 3;
    const MBR_PARTIALLY_DOMINANT = 4;
    const MBR_FULLY_DOMINANT = 5;
	
	protected static $name;
	protected static $icon;
	protected static $desc;
	protected static $bid;
    protected static $alt_id = null;
	protected static $visible = true;
    protected static $remotable = true;
    protected static $dominance = Model_Buffs_Abstract_Buff::MBR_EQUAL;
	
	protected $assoc_player;
	protected $effects = Array();
	protected $lifetime = -1;

    /**
     * Returns the buff name
     * @return string
     */
    public function name() {
		return static::$name;
	}

    public function get_dominance() {
        return static::$dominance;
    }

    /**
     * Returns the buff icon path
     * @return string
     */
    public function icon() {
		return static::static_icon();
	}

    /**
     * Returns the buff icon path
     * @return string
     */
    public static function static_icon() {
        return "/application/assets/icons/buffs/" . static::$icon . ".gif";
    }

    /**
     * Returns weather the buff should be visualized
     * @param bool $remotable Get visibillity status for remote player views
     * @return bool
     */
    public function visible($remotable = false) {
        return $remotable ? (static::$visible && static::$remotable) : static::$visible;
	}

    /**
     * Returns weather the buff should be visualized
     * @param bool $remotable Get visibillity status for remote player views
     * @return bool
     */
    public static function static_visible($remotable = false) {
        return $remotable ? (static::$visible && static::$remotable) : static::$visible;
    }

    /**
     * Returns the buff description
     * @return string
     */
    public function description() {
		return static::$desc;
	}

    /**
     * Returns the buff description
     * @return string
     */
    public static function static_description() {
        return static::$desc;
    }

    /**
     * Returns the static buff identifier for this buff
     * @return string
     */
    public function bid() {
		return static::$bid;
	}

    /**
     * Returns the static buff identifier for this buff
     * @return string
     */
    public static function static_bid() {
        return static::$bid;
    }

    /**
     * Returns the static buff alternative identifier for this buff
     * @return string
     */
    public function abid() {
        return static::$alt_id;
    }

    /**
     * Returns the static buff alternative identifier for this buff
     * @return string
     */
    public static function static_abid() {
        return static::$alt_id;
    }

    /**
     * Creates and applies the buff; if $player_id is not provided, the currently active player will be selected
     * @param number|null $player_id
     * @param number $lifetime Buff lifetime; omit or set smaller than 0 to disable auto-unbuff based on lifetime
     */
    public function __construct($player_id = NULL, $lifetime = -1) {
        /**
         * @global $game Model_Game
         */
        global $game;
		
		$this->lifetime = $lifetime;

		$this->assoc_player = $game->get_player($player_id);
		$this->assoc_player->buff_add($this);

		$this->apply();
	}

    /**
     * Applies the buff effects to the player
     */
    protected function apply() {
		$tmp = Array();
		foreach (array_keys($this->effects) as $key) {
			$tmp[] = $key;
			$tmp[] = 0;
		}
		$this->assoc_player->stats_modify($tmp);
	}

    /**
     * Removes the buff
     * @return bool
     */
    public function unbuff() {
		$this->assoc_player->buff_remove($this);
        return true;
	}

    /**
     * Recalculates the lifetime after substracting one lifetime tick; will not do anything when auto-unbuff based on lifetime is disabled
     * @return bool
     */
    public function tick() {
		if ($this->lifetime < 0) return true;
		else {
			$this->lifetime--;
			if ($this->lifetime <= 0) $this->unbuff();
		}
        return true;
	}

    /**
     * Returns the effect of this buff on a specified status bar
     * @param number $stat Status bar
     * @param number $type Effect type
     * @return int
     */
    public function effect($stat, $type) {
		if (isset($this->effects[$stat]) && isset($this->effects[$stat][$type]))
			 return $this->effects[$stat][$type];
		else return 0;
	}

    /**
     * Recalculates the effects of this buff
     * @return bool
     */
    public function rebuild() {
		return true;
	}

    /**
     * Gets called when a new buff with the same buff identifier is cast on a player; the function is called on the resident buff, with the new one as argument
     * @param Model_Buffs_Abstract_Buff $newclass
     */
    public function merge($newclass) {
		return;
	}

    /**
     * Returns the buff lifetime
     * @return number
     */
    public function lifetime() {
		return $this->lifetime;
	}

    /**
     * Called before the buff is removed via the player object
     * @return bool
     */
    public function remove() {
        return true;
    }

    public function active() {
        return true;
    }
}
