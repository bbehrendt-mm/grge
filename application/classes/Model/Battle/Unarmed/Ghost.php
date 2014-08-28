<?php defined('SYSPATH') OR die('No direct access allowed.');

abstract class Model_Battle_Unarmed_Ghost extends Model_Battle_Weapon {
	
	protected static $static_info = Array(
			'name' => 'Geisterhafte Faust',
			'icon' => 'ghost',
			'description' => '',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_FIGHT,
	);
	
	public static $range = Array(0,0);
	protected static $damage = Array(0,0);
	public static $damage_type = Model_Battle_Weapon::MBW_DMG_IMPACT;
	protected static $ammo = null;
	public static $accuracy = 0;
	public static $durability = 1;
	public static $bounce = 0;
	public static $reload_time = 5;
	public static $lock_type = Model_Battle_Weapon::MBW_LCK_PLAYER;
	public static $energy_cost = 0;
}	