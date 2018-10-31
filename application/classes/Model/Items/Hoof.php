<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Hoof extends Model_Combat_Weapon {

    protected static $damage = [4,10];
    protected static $range = [0,2];
    protected static $accuracy = 0.9;
    protected static $use_fixed_accuracy = true;
    protected static $aoe = true;
    protected static $friendly_fire = false;

    protected static $animation = Model_Combat_Weapon::MCW_ANIMATION_PUNCH;

    protected static $static_info = Array(
        'name' => 'Mächtiger Huf',
        'icon' => 'hoof',
        'description' => '',
        'category' => Model_Items_Abstract_Item::MIAI_CAT_FIGHT,
    );

    protected static $weight = 0;
}