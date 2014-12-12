<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Generic_Teddy extends Model_Items_Abstract_Item implements Interface_Static {

	protected static $static_info = Array(
			'name' => 'Teddy',
			'icon' => 'teddy',
			'description' => 'Dieser niedliche Teddy ist genau das richtige, um sich ein bisschen von der schlechten wirtschaftlichen Lage sowie der Zombieapokalypse abzulenken. Ihn nicht mitzunehmen wäre geradezu ein Verbrechen!',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_RES,
	);
	
	protected static $instances_info = Array(
			Array(	'name' => 'Mr. Knuffibonkas'),
			Array(	'name' => 'Stofftier'),
			Array(	'name' => 'Teddybär'),
			Array(	'name' => 'Staubiger Teddy'),
	);
	
	protected static $weight = 2;
}	