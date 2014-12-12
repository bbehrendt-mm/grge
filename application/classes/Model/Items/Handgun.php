<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Handgun extends Model_Battle_Weapon implements Interface_Static {

	protected static $static_info = Array(
			'name' => 'Revolver',
			'icon' => 'handgun',
			'description' => 'Batteriewerfer? HA! Mit diesem Baby nimmst du Zombies aus der Entfernung mit einem Lächeln aufs Korn!',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_FIGHT,
	);

	protected static $weight = 5;
	
	public static $range = Array(0,100);
	protected static $damage = Array(5,20);
	public static $damage_type = Model_Battle_Weapon::MBW_DMG_SCATTER;
	protected static $ammo = 'Model_Items_Ammo';
	public static $accuracy = 2.5;
	public static $accuracy_type = Model_Battle_Weapon::MBW_ACC_LINEAR_DISTANCE;
	public static $durability = 1;
	public static $bounce = 0;
	public static $reload_time = 1;
	public static $lock_type = Model_Battle_Weapon::MBW_LCK_WEAPON;
	public static $energy_cost = 0;
}	