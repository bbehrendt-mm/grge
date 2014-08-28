<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Generic_Spice extends Model_Items_Abstract_Item implements Interface_Static {
	
	protected static $static_info = Array(
			'name' => 'Gewürzmischung',
			'icon' => 'spice',
			'description' => 'Mit dieser Gewürzmischung kannst du beim Kochen sogar die langweiligste Speise aufpeppen!',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_RES,
	);

	protected static $weight = 1;
}	