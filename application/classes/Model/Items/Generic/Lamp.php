<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Generic_Lamp extends Model_Items_Abstract_Item implements Interface_Static {
	
	protected static $static_info = Array(
			'name' => 'Nachttischlampe',
			'icon' => 'lamp',
			'description' => 'Dieses hochdekorative Item sorgt für Erleuchtung - wahrscheinlich nicht bei dir, dafür aber für dein Versteck. Einziger Nachteil: Sie braucht dafür Energie...',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_RES,
	);

	protected static $weight = 6;
}	