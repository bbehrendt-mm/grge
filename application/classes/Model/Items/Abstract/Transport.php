<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Abstract_Transport extends Model_Items_Abstract_Item {

    protected static $carrier_item = true;
    protected static $max_per_player = 1;

    protected static $speedup = 0;

    public function active() {
        return true;
    }

    public function speedup() {
        return static::$speedup;
    }

    /**
     * @param Model_Player $p
     * @param number $d
     * @return bool
     */
    public function trigger_before($p, $d) {
        return $this->active();
    }

    /**
     * @param Model_Player $p
     * @param number $d
     * @return bool
     */
    public function trigger_after($p, $d) {
        return true;
    }

}	