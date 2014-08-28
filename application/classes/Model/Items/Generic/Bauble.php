<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Generic_Bauble extends Model_Battle_Weapon implements Interface_Static {

    protected static $static_info = Array(
        'name' => 'Christbaumkugel',
        'icon' => 'bauble/generic',
        'description' => 'Diese wundervolle Christbaumkugel weckt weihnachtliche Gefühle in dir - und sie weckt mörderische Gefühle in dir, wenn du daran denkst, dass du sie auch auf einen Zombie werfen kannst.',
        'category' => Model_Items_Abstract_Item::MIAI_CAT_RES,
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

    public static $range = Array(1,80);
    protected static $damage = Array(4,10);
    public static $damage_type = Model_Battle_Weapon::MBW_DMG_SCATTER;
    protected static $ammo = 'self';
    public static $accuracy = 0.7;
    public static $accuracy_type = Model_Battle_Weapon::MBW_ACC_STATIC;
    public static $durability = 1;
    public static $bounce = 0;
    public static $reload_time = 0;
    public static $lock_type = Model_Battle_Weapon::MBW_LCK_PLAYER;
    public static $energy_cost = 0;
}	