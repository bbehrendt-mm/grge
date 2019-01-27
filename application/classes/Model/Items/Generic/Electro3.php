<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Generic_Electro3 extends Model_Items_Abstract_Item implements Interface_Static {
	
	protected static $static_info = Array(
			'name' => 'Experimentelles Elektronisches Bauteil',
			'icon' => 'electro3',
			'description' => 'Gerüchte besagen, dieses hochgradig seltene Bauteil verwendet Alien-Technologie aus Area52... andere Gerüchte hingegen sagen, es handelt sich einfach um ein Atari2600 Board aus den 80ern.',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_RES,
	);

	protected static $weight = 5;
}	