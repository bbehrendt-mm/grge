<?php defined('SYSPATH') OR die('No direct access allowed.');

abstract class Model_Items_Virtual_Invoke_Abstract extends Model_Items_Abstract_Virtual {

    protected static $count_as_item = false;
    
    abstract public function trigger_spawn(Model_Places_Abstract_Place $location, Interface_Plentity $player);
    
    public function countAsItem() {
        return static::$count_as_item;
    }
}	