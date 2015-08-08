<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Generic_Bauble extends Model_Combat_Weapons_Throwable implements Interface_Static {

    protected static $static_info = Array(
        'name' => 'Christbaumkugel',
        'icon' => 'bauble/generic',
        'description' => 'Diese wundervolle Christbaumkugel weckt weihnachtliche Gefühle in dir - und sie weckt mörderische Gefühle in dir, wenn du daran denkst, dass du sie auch auf einen Zombie werfen kannst.',
        'category' => Model_Items_Abstract_Item::MIAI_CAT_RES,
        'deco' => 5,
    );

    protected static $instances_info = Array(
        Array('icon' => 'bauble/b1'),
        Array('icon' => 'bauble/b2'),
        Array('icon' => 'bauble/b3'),
        Array('icon' => 'bauble/b4'),
        Array('icon' => 'bauble/b5'),
        Array('icon' => 'bauble/b6'),
        Array('icon' => 'bauble/b7'),
    );

	protected static $weight = 3;

    protected static $damage = [4,10];
    protected static $range = [1,40];
    protected static $accuracy = 0.7;
    protected static $use_fixed_accuracy = true;
    protected static $aoe = false;
    protected static $friendly_fire = false;
    protected static $energy = 0;

    //public static $reload_time = 0;
}	