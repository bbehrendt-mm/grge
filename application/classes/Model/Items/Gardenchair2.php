<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Gardenchair2 extends Model_Items_Abstract_Chair implements Interface_Static {

	protected static $static_info = Array(
		'name' => 'Ektorp-Gluten Stuhl',
		'icon' => 'gardenchair2',
		'description' => 'Stabil und aus einem Stück gegossen - dieser Stuhl ist zwar schwerer, dafür aber auch widerstandsfähiger als ein einfacher Plastikstuhl.',
		'category' => Model_Items_Abstract_Item::MIAI_CAT_RES,
        'deco' => 2,
	);

	protected static $weight = 30;

	protected static $damage = [3,6];
	protected static $energy = 6;
	protected static $max_range = 1;

	protected static $durabillity = 0.7;

	// INI, ATK, DEF, ACC
	protected static $effects = [-4,0,0,0];
}	