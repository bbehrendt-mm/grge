<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Generic_Pumpkin extends Model_Items_Abstract_Item implements Interface_Static {
	
	protected static $static_info = Array(
			'name' => 'Zierkürbis',
			'icon' => 'pumpkin',
			'description' => 'Leider ist es nur ein Zierkürbis, du kannst ihn also nicht essen. Aber sicherlich findest du eine Verwendung für ihn. ',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_RES,
	);

	protected static $weight = 15;
}	