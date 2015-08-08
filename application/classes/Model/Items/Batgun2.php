<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Batgun2 extends Model_Combat_Weapons_Ammo implements Interface_Static {

	protected static $static_info = Array(
			'name' => 'Batteriewerfer mit verb. Ladevorrichtung',
			'icon' => 'batgun2',
			'description' => 'In mühevoller Handarbeit hast du diesem Batteriewerfer eine selbst entworfene, neue Ladevorrichtung verpasst. Eigentlich solltest du das Teil von nun an "Batterie-Maschinenwerfer" nennen ...',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_FIGHT,
	);

	protected static $weight = 10;

	protected static $ammo = ['Model_Items_Battery' => 1];
	protected static $damage = [5,10];
	protected static $range = [1,60];
	protected static $accuracy = 0.7;
	protected static $use_fixed_accuracy = false;
	protected static $aoe = false;

	//public static $reload_time = 1;
}	