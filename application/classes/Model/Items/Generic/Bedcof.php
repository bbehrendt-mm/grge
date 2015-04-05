<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Generic_Bedcof extends Model_Items_Generic_Bed {
	
	protected static $static_info = Array(
        'name' => 'Sarg-Matratze',
        'icon' => 'bed_cof',
        'description' => 'Dafür dass normalerweise nur Tote auf ihr liegen ist diese Matratze enorm bequem! ',
        'category' => Model_Items_Abstract_Item::MIAI_CAT_RES,
        'deco' => 8,
	);

	protected static $weight = 20;
}	