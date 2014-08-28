<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Knife extends Model_Battle_Weapon implements Interface_Static {

	protected static $static_info = Array(
			'name' => 'Scharfes Messer',
			'icon' => 'knife',
			'description' => 'Mit einem scharfen Messer in der Tasche bist du für alles gerüstet: Plötzliche Zombieangriffe, spontane Kochduelle, überraschende RTL-Interviews! Du solltest wirklich nie ohne Messer aus dem Haus gehen.... es sei denn, du hast eine Machete.',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_FIGHT,
	);

	protected static $weight = 10;
	protected static $essential = true;

	public static $range = Array(0,0);
	protected static $damage = Array(2,3);
	public static $damage_type = Model_Battle_Weapon::MBW_DMG_IMPACT;
	protected static $ammo = null;
	public static $accuracy = 1;
	public static $accuracy_type = Model_Battle_Weapon::MBW_ACC_STATIC;
	public static $durability = 0.8;
	public static $bounce = 1;
	public static $reload_time = 0;
	public static $lock_type = Model_Battle_Weapon::MBW_LCK_PLAYER;
	public static $energy_cost = 2;
}	