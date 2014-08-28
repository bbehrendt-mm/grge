<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Generic_Ducttape extends Model_Items_Abstract_Item implements Interface_Static {
	
	protected static $static_info = Array(
			'name' => 'Rolle Klebeband',
			'icon' => 'ducttape',
			'description' => 'Dieses Klebeband fügt zusammen, was zusammen gehört. Unerlässlich für die meisten Bastelarbeiten kann es zudem als Fliegenpapier verwendet werden.',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_RES,
	);

	protected static $weight = 2;
}	