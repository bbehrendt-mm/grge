<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Generic_Spraycan extends Model_Items_Abstract_Item implements Interface_Static {
	
	protected static $static_info = Array(
        'name' => 'Leere Spraydose',
        'icon' => 'spray/spray0',
        'description' => 'Eine leere Spraydose ist ziemlich nutzlos... aber vielleicht könntest du sie mit etwas füllen?',
        'category' => Model_Items_Abstract_Item::MIAI_CAT_RES,
        'deco' => 0,
	);

	protected static $weight = 2;
}	