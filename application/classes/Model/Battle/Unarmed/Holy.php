<?php defined('SYSPATH') OR die('No direct access allowed.');

abstract class Model_Battle_Unarmed_Holy extends Model_Battle_Weapon {		

	protected static $static_info = Array(
			'name' => 'Heilige Faust der Gerechtigkeit',
			'icon' => 'hfist',
			'description' => '',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_FIGHT,
	);
	
	public static $range = Array(0,100);
	protected static $damage = Array(60,60);
	public static $damage_type = Model_Battle_Weapon::MBW_DMG_AREA;
	protected static $ammo = null;
	public static $accuracy = 1;
	public static $durability = 1;
	public static $bounce = 0;
	public static $reload_time = 0;
	public static $lock_type = Model_Battle_Weapon::MBW_LCK_PLAYER;
	public static $energy_cost = 0;
}	