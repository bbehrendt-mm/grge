<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Fist extends Model_Combat_Weapon {

    protected static $damage = [0,2];
    protected static $range = [0,1];
    protected static $accuracy = 1;
    protected static $use_fixed_accuracy = true;
    protected static $aoe = false;
    protected static $friendly_fire = false;

    protected static $static_info = Array(
        'name' => 'Faust',
        'icon' => 'fist',
        'description' => '',
        'category' => Model_Items_Abstract_Item::MIAI_CAT_FIGHT,
    );

    protected static $weight = 0;
}