<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Smallbottle extends Model_Items_Abstract_Bottle {

	protected static $static_info = Array(
			'name' => 'Glasflasche',
			'icon' => 'smallbottle',
			'description' => 'Sie ist weder groß noch sonderlich stabil, aber du kannst trotzdem ein wenig Flüssigkeit darin aufbewahren.',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_BOTTLES,
	);

	protected static $weight = 0;
	
	protected static $capacity = 1;
}	