<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Watergun extends Model_Items_Abstract_Wbgun {
	
	protected static $static_info = Array(
			'name' => 'Aquablaster XS',
			'icon' => 'watergun',
			'description' => 'Zombies hassen Wasser - dieser Fakt verwandelt eine Wasserpistole für Kinder in ein episches Tötungswerkzeug für Zombies. Du musst nur dafür sorgen, dass der Tank immer voll ist...',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_FIGHT,
	);

	protected static $weight = 5;
	protected static $essential = true;
	
	protected static $capacity = 2;
	public static $range = Array(0,10);
	protected static $damage = Array(5,20);
	public static $damage_type = Model_Battle_Weapon::MBW_DMG_SCATTER;
	protected static $custom_icon = "water_variant";
	protected static $ammo = 'custom';
	public static $accuracy = 1.6;
	public static $accuracy_type = Model_Battle_Weapon::MBW_ACC_LINEAR_DISTANCE;
	public static $durability = 1;
	public static $bounce = 0;
	public static $reload_time = 0;
	public static $lock_type = Model_Battle_Weapon::MBW_LCK_WEAPON;
	public static $energy_cost = 0;
}	