<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Generic_Egg1 extends Model_Items_Abstract_Easteregg implements Interface_Static {

	protected static $static_info = Array(
			'name' => 'Farbiges Osterei',
			'icon' => 'eggs/e5',
			'description' => 'Du hast ein farbiges Osterei gefunden! Leider ist es schon aufgebrochen, daher kannst du es nicht essen. Naja, Corax wird dir das Ei sicher trotzdem abnehmen.',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_EVENT,
	);
	
	protected static $instances_info = Array(
        Array('icon' => 'eggs/e1'),
        Array('icon' => 'eggs/e2'),
        Array('icon' => 'eggs/e3'),
        Array('icon' => 'eggs/e4'),
        Array('icon' => 'eggs/e5'),
        Array('icon' => 'eggs/e6'),
        Array('icon' => 'eggs/e7'),
	);

    protected static $value = 0.1;
}	