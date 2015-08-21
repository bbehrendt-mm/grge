<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Nrarifle extends Model_Combat_Weapons_Ammo implements Interface_Static {

	protected static $static_info = Array(
			'name' => 'Sturmgewehr',
			'icon' => 'rifle_nra',
			'description' => 'Dieses Sturmgewehr erhält jeder, der eine lebenslange NRA-Mitgliedschaft abschließt. Achtung: Darf nur gegen Kriminelle, Auslänger, Nicht-Christen und Liberale verwendet werden.',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_FIGHT,
	);
	
	protected static $weight = 13;

	protected static $ammo = ['Model_Items_Ammo' => 1];
	protected static $damage = [5,20];
	protected static $range = [1,100];
	protected static $accuracy = 1;
	protected static $use_fixed_accuracy = false;
	protected static $aoe = true;

	protected static $animation = Model_Combat_Weapon::MCW_ANIMATION_SHOT_AMMO;

	protected static $accuracy_downscale = 0.75;
	//public static $reload_time = 0;
}	