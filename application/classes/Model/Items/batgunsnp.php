<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Batgunsnp extends Model_Battle_Weapon implements Interface_Static {

	protected static $static_info = Array(
			'name' => 'Scharfschützen-Batteriewerfer',
			'icon' => 'batgun_snp',
			'description' => 'Wenn du nicht so darauf stehst, wie Rambo wild in der Gegend rumzuballern und trotzdem nichts zu treffen, dann benutze dieses hochelegante Batterie-Scharfschützengewehr. Jeder Schuss ist äußerst tödlich und garantiert ein Treffer - vorrausgesetzt, du hast dir die Zeit zum Zielen genommen.',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_FIGHT,
	);

	protected static $weight = 12;

	public static $range = Array(50,100);
	protected static $damage = Array(80,100);
	public static $damage_type = Model_Battle_Weapon::MBW_DMG_IMPACT;
	protected static $ammo = 'Model_Items_Battery';
	public static $accuracy = 1;
	public static $accuracy_type = Model_Battle_Weapon::MBW_ACC_STATIC;
	public static $durability = 1;
	public static $bounce = 0;
	public static $reload_time = 15;
	public static $lock_type = Model_Battle_Weapon::MBW_LCK_PLAYER;
	public static $energy_cost = 0;
}	