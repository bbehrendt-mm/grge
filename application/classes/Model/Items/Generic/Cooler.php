<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Generic_Cooler extends Model_Items_Abstract_Item implements Interface_Static {
	
	protected static $static_info = Array(
			'name' => 'Kühlkreis',
			'icon' => 'cooler',
			'description' => 'Dies ist die Kernkomponente für alle Geräte, die irgendetwas kühlen sollen.',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_RES,
	);

	protected static $weight = 20;
}	