<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Gardenchair extends Model_Items_Abstract_Chair implements Interface_Static {

	protected static $static_info = Array(
		'name' => 'Plastikstuhl',
		'icon' => 'gardenchair',
		'description' => 'Dieser Stuhl sieht ziemlich unbequem und wenig stabil aus - und dreckig ist er auch noch. Dafür ist er aber wenigstens leicht genug, um damit Zombies auf Distanz zu halten.',
		'category' => Model_Items_Abstract_Item::MIAI_CAT_RES,
	);

	protected static $weight = 20;

	public static $range = Array(0,1);
	protected static $damage = Array(2,6);
	public static $damage_type = Model_Battle_Weapon::MBW_DMG_IMPACT;
	protected static $ammo = null;
	public static $accuracy = 1;
	public static $accuracy_type = Model_Battle_Weapon::MBW_ACC_STATIC;
	public static $durability = 0.6;
	public static $bounce = 2;
	public static $reload_time = 1;
	public static $lock_type = Model_Battle_Weapon::MBW_LCK_PLAYER;
	public static $energy_cost = 5;
}	