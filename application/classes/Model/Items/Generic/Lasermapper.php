<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Generic_Lasermapper extends Model_Items_Abstract_Item implements Interface_Static {
	
	protected static $static_info = Array(
			'name' => 'Lasermessgerät',
			'icon' => 'maptool2',
			'description' => 'Wenn du es richtig bedienst, kann dir dieses Ding beim Kartographieren einer Ruine sehr viel Arbeit abnehmen.',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_GEAR,
	);

	protected static $weight = 9;
}	