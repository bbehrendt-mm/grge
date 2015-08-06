<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Test_Claw extends Model_Combat_Weapon {

    protected static $damage = [0,1];
    protected static $range = [0,1];
    protected static $accuracy = 0.5;
    protected static $use_fixed_accuracy = true;
    protected static $aoe = false;
    protected static $friendly_fire = false;

    protected static $static_info = Array(
        'name' => 'Verfaulte Klaue',
        'icon' => 'claw',
        'description' => '',
        'category' => Model_Items_Abstract_Item::MIAI_CAT_FIGHT,
    );

    protected static $weight = 0;
}