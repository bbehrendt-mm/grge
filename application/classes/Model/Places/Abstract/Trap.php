<?php defined('SYSPATH') OR die('No direct access allowed.');

abstract class Model_Places_Abstract_Trap extends Model_Places_Abstract_Place {

    protected static $max_zombie_num = 4;
    protected static $chance = 0.1;

    public function enter($pid = null, $type = Interface_Tickable::IT_TYPE_PLAYER): bool
    {
        parent::enter($pid, $type);

        if (random_int(0,100) < (100*static::$chance) && !$this->zombie_factory()->accumulation() && !Tool_Scripts::at_location($this->uin(), true, true))
            $this->zombie_factory()->accumulation(random_int(1,static::$max_zombie_num));
    }
}	