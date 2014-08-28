<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Generic_Belt extends Model_Items_Abstract_Item implements Interface_Static {
	
	protected static $static_info = Array(
			'name' => 'Riemen',
			'icon' => 'belt',
			'description' => 'Wenn du in Physik aufgepasst hättest, wüsstest du das Riemen in vielen mechanischen Anlagen unerlässlich sind. Ihre wichtigste Funktion ist, genau im falschen Moment zu reißen, was im Allgemeinen zu lustigen und gelegentlich tödlichen Situationen führt.',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_RES,
	);

	protected static $weight = 1;
}	