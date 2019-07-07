<?php defined('SYSPATH') OR die('No direct access allowed.');

abstract class Model_Items_Abstract_Virtual extends Model_Items_Abstract_Item {
	protected static $weight = 0;
    protected static $graceful_fail = false;
    protected static $manual_ui = false;

    protected static $store_location_data = true;
    protected $loc_id = -1;
    protected $room_id = -1;

    protected static $default_action_uses = false;

    /** @var bool|array $remaining  */
    protected $remaining;

    public function __construct($type = null) {
        parent::__construct($type);
        $this->remaining = static::$default_action_uses;
    }

    protected function location() {
        return $this->loc_id >= 0 ? Globals::CurrentGameF()->location($this->loc_id) : null;
    }

    /**
     * @return Model_Room|null
     */
    protected function room(): ?Model_Room {
        return ($this->loc_id >= 0 && $this->room_id >= 0 && $this->location() !== null) ? $this->location()->room($this->room_id) : null;
    }

    /**
     * @return Model_Room
     */
    protected function roomF(): Model_Room {
        $r = $this->room();
        if ($r === null) throw new RuntimeException('Accessed non-specified room property from item.');
        return $r;
    }

    public static function setup_location(): bool {
        return static::$store_location_data;
    }

    public function set_location_info($lid, $rid = null): void {
        $this->loc_id = $lid;
        if ($rid !== null)  $this->room_id = $rid;
    }

    public function use_manual_ui(): bool {
        return static::$manual_ui;
    }

    public function has_action($action): bool {
        if ($this->remaining === false) return true;

        return isset($this->remaining[$action]);
    }

    public function set_remaining_actions($set): void {
        if ($this->remaining === false)
            return;

        foreach ($this->remaining as $action => &$num)
            $num = $set;
    }

    public function remaining_actions($action = null, $set = null) {
        if ($this->remaining === false)
            return PHP_INT_MAX;

        if ($action === null)
            return array_sum($this->remaining);

        if ($set !== null)
            return $this->remaining[$action] = $set;

        if (!isset($this->remaining[$action]))
            return 0;
        else return (int)$this->remaining[$action];
    }

    public function interact($action, $player, $argument = NULL, $side_player = null) {
        if ($this->remaining_actions($action)) {
            $preserve = !parent::interact($action, $player, $argument, $side_player) && static::$graceful_fail;
            if ($this->remaining !== false && !$preserve && $this->remaining[$action] < PHP_INT_MAX)
                $this->remaining[$action]--;
        }
    }

    public function can_take(&$message): bool {
        return false;
    }

    public function can_drop(&$message, $p = null): bool {
        return false;
    }

    public function drop_dead() {
        return null;
    }
}	