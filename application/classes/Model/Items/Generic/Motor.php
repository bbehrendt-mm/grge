<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Generic_Motor extends Model_Items_Abstract_Item implements Interface_Static {
	
	protected static $static_info = Array(
			'name' => 'Motor',
			'icon' => 'motor',
			'description' => 'Dieser Motor ist ein Wunderwerk der Technik, ausgezeichnet durch geringen Abgaßausstoß und hohe Energieeffizienz. Leider nützt er dir nicht viel ohne Kraftstoff...',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_RES,
	);

	protected static $weight = 50;
}	