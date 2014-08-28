<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Generic_Egg2 extends Model_Items_Abstract_Easteregg implements Interface_Static {

	protected static $static_info = Array(
			'name' => 'Prächtiges Osterei',
			'icon' => 'eggs/pe3',
			'description' => 'Du hast ein prächtiges Osterei gefunden! Es ist bunt bemalt, kunstvoll verziehrt und fast völlig unbeschädigt - das Teil ist sicher einiges wert!',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_EVENT,
	);
	
	protected static $instances_info = Array(
        Array('icon' => 'eggs/pe1'),
        Array('icon' => 'eggs/pe2'),
        Array('icon' => 'eggs/pe3'),
        Array('icon' => 'eggs/pe4'),
        Array('icon' => 'eggs/pe5')
	);

    protected static $value = 1;
}	