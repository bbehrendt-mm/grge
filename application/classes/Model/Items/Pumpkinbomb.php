<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Pumpkinbomb extends Model_Battle_Weapon implements Interface_Static {

	protected static $static_info = Array(
			'name' => 'Kürbisbombe',
			'icon' => 'pumpkinbomb',
			'description' => 'Süßes sonst gibts Saures! Diese Kürbisbombe ist äußerst effektiv gegen in der Nähe herumstehende Zombies, die dir einfach keine Bonbons geben wollen.',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_FIGHT,
	);
	
	protected static $weight = 10;

	public static $range = Array(1,8);
	protected static $damage = Array(60,100);
	public static $damage_type = Model_Battle_Weapon::MBW_DMG_AREA;
	protected static $ammo = 'self';
	public static $accuracy = 0.85;
	public static $accuracy_type = Model_Battle_Weapon::MBW_ACC_STATIC;
	public static $durability = 1;
	public static $bounce = 0;
	public static $reload_time = 5;
	public static $lock_type = Model_Battle_Weapon::MBW_LCK_PLAYER;
	public static $energy_cost = 10;
}	