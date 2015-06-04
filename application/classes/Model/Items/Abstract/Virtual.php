<?php defined('SYSPATH') OR die('No direct access allowed.');

abstract class Model_Items_Abstract_Virtual extends Model_Items_Abstract_Item {
	protected static $weight = 0;
    protected static $graceful_fail = false;
    protected static $manual_ui = false;

    protected $remaining = false;

    public function use_manual_ui() {
        return static::$manual_ui;
    }

    public function remaining_actions($action = null) {
        if ($this->remaining === false)
            return PHP_INT_MAX;

        if ($action === null)
            return array_sum($this->remaining);

        if (!isset($this->remaining[$action]))
            return 0;
        else return (int)$this->remaining[$action];
    }

    public function interact($action, $argument = NULL, $side_player = null) {
        if ($this->remaining_actions($action)) {
            $preserve = !parent::interact($action, $argument, $side_player) && static::$graceful_fail;
            if ($this->remaining !== false && !$preserve && $this->remaining[$action] < PHP_INT_MAX)
                $this->remaining[$action]--;
        }
    }

    public function take($silent = false) {
        return false;
    }

    public function drop() {
        return false;
    }

    public function drop_dead() {
        return null;
    }
}	