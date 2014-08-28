<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Batgun3 extends Model_Battle_Weapon implements Interface_Static {

	protected static $static_info = Array(
			'name' => 'Batteriewerfer MK II',
			'icon' => 'batgun3',
			'description' => 'Der Batteriewerfer MK II ist die Bazooka unter den Batteriewerfern. Durch den automatischen Druckregler kannst du sowohl weiter schießen als auch genauer zielen. Damit wird dein Batteriewerfer zur absolut tödlichen Waffe! ...',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_FIGHT,
	);

	protected static $weight = 10;
	
	public static $range = Array(1,80);
	protected static $damage = Array(7,15);
	public static $damage_type = Model_Battle_Weapon::MBW_DMG_IMPACT;
	protected static $ammo = 'Model_Items_Battery';
	public static $accuracy = 0.8;
	public static $accuracy_type = Model_Battle_Weapon::MBW_ACC_LINEAR_DISTANCE;
	public static $durability = 1;
	public static $bounce = 0;
	public static $reload_time = 1;
	public static $lock_type = Model_Battle_Weapon::MBW_LCK_WEAPON;
	public static $energy_cost = 0;
}	