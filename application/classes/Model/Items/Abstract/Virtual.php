<?php defined('SYSPATH') OR die('No direct access allowed.');

abstract class Model_Items_Abstract_Virtual extends Model_Items_Abstract_Item {
	protected static $weight = 0;

    protected $remaining = array();

    public function remaining_actions($action = null) {
        if ($action === null)
            return array_sum($this->remaining);

        if (!isset($this->remaining[$action]))
            return 0;
        else return (int)$this->remaining[$action];
    }

    public function interact($action, $argument = NULL, $side_player = null) {
        if ($this->remaining_actions($action)) {
            parent::interact($action, $argument, $side_player);
            if ($this->remaining[$action] < PHP_INT_MAX)
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