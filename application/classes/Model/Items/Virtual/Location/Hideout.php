<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Virtual_Location_Hideout extends Model_Items_Abstract_Virtual {

    protected static $default_action_uses = array(
        'hideout_builder' => PHP_INT_MAX,
        'hideout_maker' => PHP_INT_MAX,
        'hideout_sleep' => PHP_INT_MAX,
        'hideout_defense' => PHP_INT_MAX,
        'hideout_couch' => PHP_INT_MAX,
    );

}	