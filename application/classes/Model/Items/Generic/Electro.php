<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Generic_Electro extends Model_Items_Abstract_Item implements Interface_Static {
	
	protected static $static_info = Array(
			'name' => 'Elektronisches Bauteil',
			'icon' => 'electro',
			'description' => 'Es ist gar nicht wichtig, was dieses Teil mal war oder wozu es gut ist. Es ist eine Leiterplatte mit Leiterbahnen und diversen Schickschnack drauf - steck es einfach in eine von deinen Konstruktionen und es wird schon irgendwie funktionieren!',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_RES,
	);

	protected static $weight = 5;
}	