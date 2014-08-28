<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Generic_Pressure extends Model_Items_Abstract_Item implements Interface_Static {
	
	protected static $static_info = Array(
			'name' => 'Druckregler',
			'icon' => 'pressure',
			'description' => 'Dieses unscheinbare Bauteil ist unglaublich selten und wertvoll! Du solltest es unbedingt mitnehmen, möglicherweise kann es deine Zombie- oder Wasserprobleme lösen...',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_RES,
	);

	protected static $weight = 20;
}	