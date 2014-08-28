<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Generic_Crwood extends Model_Items_Abstract_Item implements Interface_Static {
	
	protected static $static_info = Array(
			'name' => 'Holzabfall',
			'icon' => 'crwood',
			'description' => 'Dieser Holzabfall ist alleine nicht viel Wert, aber wenn du genug davon sammelst kannst du eventuell ein Holzbrett daraus herstellen.',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_RES,
	);

	protected static $weight = 7;
}	