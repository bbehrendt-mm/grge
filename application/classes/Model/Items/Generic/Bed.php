<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Generic_Bed extends Model_Items_Abstract_Item implements Interface_Static {
	
	protected static $static_info = Array(
        'name' => 'Matratze',
        'icon' => 'bed',
        'description' => 'Eine eingestaubte, aber noch immer bequeme Matratze. Wenn du sie mitnimmst kannst du damit garantiert dein Versteck etwas aufmöbeln!',
        'category' => Model_Items_Abstract_Item::MIAI_CAT_RES,
        'deco' => 2,
	);

	protected static $weight = 50;
}	