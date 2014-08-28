<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Generic_Sum extends Model_Items_Abstract_Item implements Interface_Static {
	
	protected static $static_info = Array(
			'name' => 'Schrauben, Muttern, Zahnräder',
			'icon' => 'sum',
			'description' => 'In dieser bunten Mischung mechanischer Kleinteile findest du eigentlich immer, was du brauchst - es sei denn, du brauchst was gegen die Zombies.',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_RES,
	);

	protected static $weight = 1;
}	