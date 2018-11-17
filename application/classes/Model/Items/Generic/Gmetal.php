<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Generic_Gmetal extends Model_Items_Generic_Metal {
	
	protected static $static_info = Array(
			'name' => 'Goldklumpen',
			'icon' => 'gmetal',
			'description' => 'Warum normales Metall verbauen wenn man auch einfach GOLD verbauen kann?',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_RES,
	);

	protected static $weight = 50;
}	