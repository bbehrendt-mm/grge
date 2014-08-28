<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Bodybag4 extends Model_Items_Abstract_Item implements Interface_Static {

	protected static $static_info = Array(
			'name' => 'Zerrissener Leichensack',
			'icon' => 'bodybag4',
			'description' => 'Dieser Leichensack ist sicherlich praktisch - wäre da nicht dieses klaffende Loch. Hiermit kannst du nichts transportieren, aber du könntest es zuhause auf deiner Werkbank flicken...',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_GEAR,
	);
	
	protected static $weight = 3;
}	