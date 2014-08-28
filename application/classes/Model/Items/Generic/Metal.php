<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Generic_Metal extends Model_Items_Abstract_Item implements Interface_Static {
	
	protected static $static_info = Array(
			'name' => 'Alteisen',
			'icon' => 'metal',
			'description' => 'Alteisen ist ein grundlegendes Baumaterial und wird für viele verschiedene Konstruktionen benötigt. Eigentlich kann man davon gar nicht genug haben.',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_RES,
	);

	protected static $weight = 10;
}	