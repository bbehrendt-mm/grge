<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Hacksaw extends Model_Battle_Weapon implements Interface_Static {

	protected static $static_info = Array(
			'name' => 'Metallsäge',
			'icon' => 'hacksaw',
			'description' => 'Beim Anblick dieser Säge läuft dir ein kalter Schauer den Rücken herunter...',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_FIGHT,
	);

	protected static $weight = 5;

	public static $range = Array(0,0);
	protected static $damage = Array(4,9);
	public static $damage_type = Model_Battle_Weapon::MBW_DMG_IMPACT;
	protected static $ammo = null;
	public static $accuracy = 1;
	public static $accuracy_type = Model_Battle_Weapon::MBW_ACC_STATIC;
	public static $durability = 1;
	public static $bounce = 1;
	public static $reload_time = 0;
	public static $lock_type = Model_Battle_Weapon::MBW_LCK_PLAYER;
	public static $energy_cost = 8;
}	