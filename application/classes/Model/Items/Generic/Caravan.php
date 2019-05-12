<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Generic_Caravan extends Model_Items_Abstract_Item implements Interface_Static {
	
	protected static $static_info = Array(
			'name' => 'Wohnwagenanhänger',
			'icon' => 'caravan',
			'description' => 'Wenn du über einen Wohnwagen verfügst, kannst du mithilfe dieses Anhängers einen neuen Raum hinzufügen - vorrausgesetzt, du bekommst dieses Teil zu deinem Wohnwagen gezogen.',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_MISC,
	);

	protected static $weight = 95;
}