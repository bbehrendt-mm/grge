<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Gardenchair2 extends Model_Items_Abstract_Chair implements Interface_Static {

	protected static $static_info = Array(
		'name' => 'Ektorp-Gluten Stuhl',
		'icon' => 'gardenchair2',
		'description' => 'Stabil und aus einem Stück gegossen - dieser Stuhl ist zwar schwerer, dafür aber auch widerstandsfähiger als ein einfacher Plastikstuhl.',
		'category' => Model_Items_Abstract_Item::MIAI_CAT_RES,
	);

	protected static $weight = 30;
	
	public static $range = Array(0,1);
	protected static $damage = Array(3,6);
	public static $damage_type = Model_Battle_Weapon::MBW_DMG_IMPACT;
	protected static $ammo = null;
	public static $accuracy = 1;
	public static $accuracy_type = Model_Battle_Weapon::MBW_ACC_STATIC;
	public static $durability = 0.7;
	public static $bounce = 3;
	public static $reload_time = 1;
	public static $lock_type = Model_Battle_Weapon::MBW_LCK_PLAYER;
	public static $energy_cost = 6;
}	