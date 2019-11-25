<?php defined('SYSPATH') OR die('No direct access allowed.');

abstract class Model_Buffs_Abstract_Buff extends Model {

	public const MB_RAISE_ACC = 1;
	public const MB_RAISE_PRC = 2;
	public const MB_DROP_ACC  = 3;
	public const MB_DROP_PRC  = 4;

    public const MBR_FULLY_RECESSIVE = 1;
    public const MBR_PARTIALLY_RECESSIVE = 2;
    public const MBR_EQUAL = 3;
    public const MBR_PARTIALLY_DOMINANT = 4;
    public const MBR_FULLY_DOMINANT = 5;

    /** @var string|null $name */
	protected static $name;
    /** @var string|null $icon */
	protected static $icon;
    /** @var string|null $desc */
	protected static $desc;
    /** @var string|null $bid */
	protected static $bid;
    protected static $alt_id ;
	protected static $visible = true;
    protected static $remotable = true;
    protected static $dominance = Model_Buffs_Abstract_Buff::MBR_EQUAL;
    protected static $allow_npc_assoc = true;

    /** @var Interface_Plentity  */
	protected $assoc_player;

	protected $lifetime = -1;

	protected function get_effects(): array { return []; }

    /**
     * Returns the buff name
     *
     * @return string
     */
    public function name(): string {
		return static::$name ?: '???';
	}

    public function get_dominance(): int
    {
        return static::$dominance;
    }

    /**
     * Returns the buff icon path
     *
     * @return string
     */
    public function icon(): string {
		return static::static_icon();
	}

    /**
     * Returns the buff name
     *
     * @return string
     */
    public static function static_name(): string {
        return static::$name ?: '???';
    }

    /**
     * Returns the buff icon path
     *
     * @return string
     */
    public static function static_icon(): string {
        return 'buffs/' . (static::$icon ?: 'any');
    }

    /**
     * Returns weather the buff should be visualized
     *
     * @param bool $remotable Get visibility status for remote player views
     *
     * @return bool
     */
    public function visible($remotable = false): bool {
        return $remotable ? (static::$visible && static::$remotable) : static::$visible;
	}

    /**
     * Returns weather the buff should be visualized
     *
     * @param bool $remotable Get visibility status for remote player views
     *
     * @return bool
     */
    public static function static_visible($remotable = false): bool {
        return $remotable ? (static::$visible && static::$remotable) : static::$visible;
    }

    /**
     * Returns the buff description
     *
     * @return string
     */
    public function description(): string {
		return static::$desc ?: '';
	}

    /**
     * Returns the buff description
     *
     * @return string
     */
    public static function static_description(): string {
        return static::$desc ?: '';
    }

    /**
     * Returns the static buff identifier for this buff
     *
     * @return string
     */
    public function bid(): string {
		return static::$bid ?: 'null';
	}

    /**
     * Returns the static buff identifier for this buff
     *
     * @return string
     */
    public static function static_bid(): string {
        return static::$bid ?: 'null';
    }

    /**
     * Returns the static buff alternative identifier for this buff
     *
     * @return string
     */
    public function abid(): string {
        return static::$alt_id;
    }

    /**
     * Returns the static buff alternative identifier for this buff
     *
     * @return string
     */
    public static function static_abid(): string {
        return static::$alt_id;
    }

    /**
     * Creates and applies the buff; if $player_id is not provided, the currently active player will be selected
     * @param Interface_Plentity|number|null $association
     * @param int|number $lifetime Buff lifetime; omit or set smaller than 0 to disable auto-unbuff based on lifetime
     * @throws Exception
     */
    public function __construct($association = NULL, $lifetime = -1) {
		$this->lifetime = $lifetime;

        if ($association === null)
            $this->assoc_player = Globals::CurrentGameF()->get_player();
        elseif (is_object($association) && Tool_System::instance_of($association, 'Interface_Plentity'))
            $this->assoc_player = $association;
        else $this->assoc_player = Globals::CurrentGameF()->get_player($association);

        if (!$this->assoc_player) throw new RuntimeException('Invalid buff association!');

        $this->assoc_player->get_status()->add($this);

		$this->apply();
	}

    protected function associated(): bool
    {
        return !empty($this->assoc_player);
    }

    protected function associated_to_player(): bool
    {
        return Tool_System::instance_of($this->assoc_player, 'Model_Player');
    }

    /**
     * Applies the buff effects to the player
     */
    protected function apply(): void
    {
		$tmp = Array();
		foreach (array_keys($this->get_effects()) as $key) {
			$tmp[] = $key;
			$tmp[] = 0;
		}
		$this->assoc_player->get_status()->modify($tmp, Model_Status::MS_EFFECT_BUFF);
	}

    /**
     * Removes the buff
     *
     * @return bool
     */
    public function unbuff(): bool {
		$this->assoc_player->get_status()->remove($this);
        return true;
	}

    /**
     * Recalculates the lifetime after substracting one lifetime tick; will not do anything when auto-unbuff based on lifetime is disabled
     *
     * @return bool
     */
    public function tick(): bool {
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
     * @return float
     */
    public function effect($stat, $type): float {
		if (!$this->active()) return 0;
        $e = $this->get_effects();
        if (isset($e[$stat][$type]))
			 return $e[$stat][$type];
		else return 0;
	}

    /**
     * Recalculates the effects of this buff
     *
     * @return bool
     */
    public function rebuild(): bool {
		return true;
	}

    /**
     * Gets called when a new buff with the same buff identifier is cast on a player; the function is called on the resident buff, with the new one as argument
     *
     * @param Model_Buffs_Abstract_Buff $newclass
     */
    public function merge(Model_Buffs_Abstract_Buff $newclass): void {}

    /**
     * Returns the buff lifetime
     * @return number
     */
    public function lifetime() {
		return $this->lifetime;
	}

    /**
     * Called before the buff is removed via the player object
     *
     * @return bool
     */
    public function remove(): bool {
        return true;
    }

    public function active(): bool
    {
        return true;
    }
}
