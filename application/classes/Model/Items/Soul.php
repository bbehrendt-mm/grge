<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Soul extends Model_Items_Abstract_Ammo implements Interface_Autotaker, Interface_Tmpitem, Interface_Static {
	
	protected static $static_info = Array(
			'name' => 'Verirrte Seele',
			'icon' => 'soul',
			'description' => 'Dies ist eine verirrte Seele, die von deinem Seelenfänger-zeichen angelockt wurde. Erlöse sie, indem du sie dem Seelensammler übergibst!',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_EVENT,
	);
	
	protected static $weight = 0;	
	protected static $autospawn = Array(1,1);
	protected static $autoappender = Array('Seele', 'Seelen');
}	