<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Gardenchair extends Model_Items_Abstract_Chair implements Interface_Static {

	protected static $static_info = Array(
		'name' => 'Plastikstuhl',
		'icon' => 'gardenchair',
		'description' => 'Dieser Stuhl sieht ziemlich unbequem und wenig stabil aus - und dreckig ist er auch noch. Dafür ist er aber wenigstens leicht genug, um damit Zombies auf Distanz zu halten.',
		'category' => Model_Items_Abstract_Item::MIAI_CAT_RES,
        'deco' => 1,
	);

	protected static $weight = 20;

	protected static $damage = [2,6];
	protected static $energy = 5;
	protected static $max_range = 1;

	//public static $durability = 0.6;
	//public static $reload_time = 1;
}	