<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Batgun extends Model_Combat_Weapons_Ammo implements Interface_Static {

	protected static $static_info = Array(
			'name' => 'Selbstgebauter Batteriewerfer',
			'icon' => 'batgun',
			'description' => 'Eigentlich ist es nur eine leicht modifizierte Kartoffelkanone, die man mit Batterien beladen kann. Sie schießt nicht sehr weit, und zielen kann man auch nicht sonderlich gut damit - aber 100mal besser als die Zombies mit bloßen Händen K.O. schlagen zu müssen, oder?',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_FIGHT,
	);

	protected static $weight = 8;

	protected static $ammo = ['Model_Items_Battery' => 1];
	protected static $damage = [5,10];
	protected static $range = [1,60];
	protected static $accuracy = 0.7;
	protected static $use_fixed_accuracy = false;
	protected static $aoe = false;

	//public static $reload_time = 2;
}	