<?php defined('SYSPATH') OR die('No direct access allowed.');

abstract class Model_Places_Abstract_Trap extends Model_Places_Abstract_Place {

    protected static $max_zombie_num = 4;
    protected static $chance = 0.1;

    public function enter($pid = null) {
        parent::enter($pid);

        if (!$this->zombie_factory()->accumulation() && !Tool_Scripts::at_location($this->uin()) && mt_rand(0,100) < (100*static::$chance))
            $this->zombie_factory()->accumulation(mt_rand(1,static::$max_zombie_num));
    }
}	