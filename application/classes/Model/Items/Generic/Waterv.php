<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Generic_Waterv extends Model_Items_Abstract_Liquid {
	
	protected static $static_info = Array(
			'name' => 'Wasser aus deiner Flasche',
			'icon' => 'water_variant',
			'description' => 'Dieses Wasser stammt aus einer deiner Flaschen. Du willst es doch hier nicht versickern lassen, oder?',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_FOOD,
	);	
}	