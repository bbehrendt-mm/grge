<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Generic_Boiler extends Model_Items_Abstract_Item implements Interface_Static {
	
	protected static $static_info = Array(
			'name' => 'Wasserkocher',
			'icon' => 'boiler',
			'description' => 'Dieser Wasserkocher ist vielseitig einsetzbar - er kann zum Beispiel Wasser kochen. Und das ist nur eine seiner besonderen Fähigkeiten!',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_RES,
	);

	protected static $weight = 10;
}	