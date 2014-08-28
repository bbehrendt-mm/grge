<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Macheteduo extends Model_Battle_Weapon implements Interface_Static {

	protected static $static_info = Array(
			'name' => '2 Macheten',
			'icon' => 'machete_dual',
			'description' => 'Es gibt nichts cooleres als mit zwei Macheten in der Hand wie ein Samurai durch Zombiehorden zu pflügen. Durch die ganze abgefahrene Choreographie verbrauchst du zwar einiges an Energie, aber das ist es wert!',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_FIGHT,
	);

	protected static $weight = 20;
	protected static $essential = true;
	
	public static $range = Array(0,3);
	protected static $damage = Array(9,20);
	public static $damage_type = Model_Battle_Weapon::MBW_DMG_AREA;
	protected static $ammo = null;
	public static $accuracy = 1;
	public static $accuracy_type = Model_Battle_Weapon::MBW_ACC_STATIC;
	public static $durability = 1;
	public static $bounce = 1;
	public static $reload_time = 0;
	public static $lock_type = Model_Battle_Weapon::MBW_LCK_PLAYER;
	public static $energy_cost = 10;
}	