<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Machete2 extends Model_Battle_Weapon implements Interface_Static {

	protected static $static_info = Array(
			'name' => 'Scharfe Machete',
			'icon' => 'machete2',
			'description' => 'Nachdem du den Rost abgeschliffen und die Klinge geschärft hast gleitet deine Machete nun wie Butter durch Zombiehorden.',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_FIGHT,
	);

	protected static $weight = 10;
	protected static $essential = true;
	
	public static $range = Array(0,1);
	protected static $damage = Array(5,12);
	public static $damage_type = Model_Battle_Weapon::MBW_DMG_SCATTER;
	protected static $ammo = null;
	public static $accuracy = 1;
	public static $accuracy_type = Model_Battle_Weapon::MBW_ACC_STATIC;
	public static $durability = 1;
	public static $bounce = 1;
	public static $reload_time = 1;
	public static $lock_type = Model_Battle_Weapon::MBW_LCK_PLAYER;
	public static $energy_cost = 3;
}	