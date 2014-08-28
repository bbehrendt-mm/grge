<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Generic_Gwood extends Model_Items_Generic_Wood implements Interface_Static {
	
	protected static $static_info = Array(
			'name' => 'Goldenes Holzbrett',
			'icon' => 'gwood',
			'description' => 'Warum normales Holz verbauen, wenn man GOLDENES Holz verbauen kann?',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_RES,
	);

	protected static $weight = 50;
}	