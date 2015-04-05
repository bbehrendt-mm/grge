<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Generic_Oven extends Model_Items_Abstract_Item implements Interface_Static {
	
	protected static $static_info = Array(
        'name' => 'Alter Ofen',
        'icon' => 'oven',
        'description' => 'Dieser alte, aber noch funktionstüchtige Ofen wird dir helfen, in deiner heimischen Küche !',
        'category' => Model_Items_Abstract_Item::MIAI_CAT_RES,
        'deco' => 1,
	);

	protected static $weight = 70;
}	