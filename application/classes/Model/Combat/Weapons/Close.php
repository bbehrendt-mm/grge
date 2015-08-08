<?php defined('SYSPATH') OR die('No direct access allowed.');

abstract class Model_Combat_Weapons_Close extends Model_Combat_Weapons_Energy {

    protected static $max_range = 1;

    protected static $accuracy = 1;
    protected static $use_fixed_accuracy = true;
    protected static $aoe = false;
    protected static $friendly_fire = false;

    protected function range() {
        return [0, $this->max_range()];
    }

    public function max_range() {
        return static::$max_range;
    }
}