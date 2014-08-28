<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Generic_Mixer extends Model_Items_Abstract_Item implements Interface_Static {
	
	protected static $static_info = Array(
			'name' => 'Rostiger Handmixer',
			'icon' => 'mixer',
			'description' => 'Dieser Handmixer hat schon bessere Tage gesehen... Naja, wenigstens verleiht er allen Speisen, die du mit seiner Hilfe zubereitest, ein würziges Rost-Aroma. Lecker!',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_RES,
	);

	protected static $weight = 6;
}	