<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Batgun extends Model_Battle_Weapon implements Interface_Static {

	protected static $static_info = Array(
			'name' => 'Selbstgebauter Batteriewerfer',
			'icon' => 'batgun',
			'description' => 'Eigentlich ist es nur eine leicht modifizierte Kartoffelkanone, die man mit Batterien beladen kann. Sie schießt nicht sehr weit, und zielen kann man auch nicht sonderlich gut damit - aber 100mal besser als die Zombies mit bloßen Händen K.O. schlagen zu müssen, oder?',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_FIGHT,
	);

	protected static $weight = 8;
	
	public static $range = Array(1,60);
	protected static $damage = Array(5,10);
	public static $damage_type = Model_Battle_Weapon::MBW_DMG_IMPACT;
	protected static $ammo = 'Model_Items_Battery';
	public static $accuracy = 0.7;
	public static $accuracy_type = Model_Battle_Weapon::MBW_ACC_LINEAR_DISTANCE;
	public static $durability = 1;
	public static $bounce = 0;
	public static $reload_time = 2;
	public static $lock_type = Model_Battle_Weapon::MBW_LCK_WEAPON;
	public static $energy_cost = 0;
}	