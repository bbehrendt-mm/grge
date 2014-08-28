<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Generic_Tube extends Model_Items_Abstract_Item implements Interface_Static {
	
	protected static $static_info = Array(
			'name' => 'Kupferrohr',
			'icon' => 'tube',
			'description' => 'Es ist nicht leicht, ein intaktes Kupferrohr zu finden. Freu dich, dieses Rohr ist vielseitig einsetzbar!',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_RES,
	);

	protected static $weight = 5;
}	