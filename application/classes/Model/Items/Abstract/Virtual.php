<?php defined('SYSPATH') OR die('No direct access allowed.');

abstract class Model_Items_Abstract_Virtual extends Model_Items_Abstract_Item {
	protected static $weight = 0;
    protected static $graceful_fail = false;
    protected static $manual_ui = false;

    protected static $store_location_data = true;
    protected $loc_id = -1;
    protected $room_id = -1;

    /** @var bool|array $remaining  */
    protected $remaining = false;

    protected function location() {
        /**
         * @global Model_Game $game
         */
        global $game;
        return $this->loc_id >= 0 ? $game->location($this->loc_id) : null;
    }

    protected function room() {
        return ($this->loc_id >= 0 && $this->room_id >= 0) ? $this->location()->room($this->room_id) : null;
    }

    public static function setup_location() {
        return static::$store_location_data;
    }

    public function set_location_info($lid, $rid = null) {
        $this->loc_id = $lid;
        if ($rid !== null)  $this->room_id = $rid;
    }

    public function use_manual_ui() {
        return static::$manual_ui;
    }

    public function has_action($action) {
        if ($this->remaining === false) return true;

        return isset($this->remaining[$action]);
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

    public function take($silent = false) {
        return false;
    }

    public function drop($p = null, $silent = false) {
        return false;
    }

    public function drop_dead() {
        return null;
    }
}	