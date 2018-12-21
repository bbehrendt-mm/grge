<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Soul2 extends Model_Items_Abstract_Ammo implements Interface_Autotaker, Interface_Tmpitem, Interface_Static {
	
	protected static $static_info = Array(
			'name' => 'Gequälte Seele',
			'icon' => 'soul2',
			'description' => 'Dies ist eine gequälte Seele, die von deinem Seelenfänger-zeichen angelockt wurde. Diese Seele ist besonders wertvoll, daher solltest du sie unbedingt dem Seelenfänger übergeben!',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_EVENT,
	);
	
	protected static $weight = 0;
	protected static $autoappender = Array('Seele', 'Seelen');
}	