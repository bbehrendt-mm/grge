<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Batgun3 extends Model_Combat_Weapons_Ammo implements Interface_Static {

	protected static $static_info = Array(
			'name' => 'Batteriewerfer MK II',
			'icon' => 'batgun3',
			'description' => 'Der Batteriewerfer MK II ist die Bazooka unter den Batteriewerfern. Durch den automatischen Druckregler kannst du sowohl weiter schießen als auch genauer zielen. Damit wird dein Batteriewerfer zur absolut tödlichen Waffe! ...',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_FIGHT,
	);

	protected static $weight = 10;

	protected static $ammo = ['Model_Items_Battery' => 1];
	protected static $damage = [7,15];
	protected static $range = [1,80];
	protected static $accuracy = 0.8;
	protected static $use_fixed_accuracy = false;
	protected static $aoe = false;

	//public static $reload_time = 1;
}	