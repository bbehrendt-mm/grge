<?php defined('SYSPATH') OR die('No direct access allowed.');

abstract class Model_Items_Abstract_Chair extends Model_Battle_Weapon {
	
	protected static $static_info = Array(
			'name' => 'Beliebiger Stuhl',
			'icon' => 'generic_chair',
			'description' => '',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_RES,
	);
}	