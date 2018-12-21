<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Handgun extends Model_Combat_Weapons_Ammo implements Interface_Static {

	protected static $static_info = Array(
			'name' => 'Revolver',
			'icon' => 'handgun',
			'description' => 'Batteriewerfer? HA! Mit diesem Baby nimmst du Zombies aus der Entfernung mit einem Lächeln aufs Korn!',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_FIGHT,
	);

	protected static $weight = 5;

	protected static $ammo = ['Model_Items_Ammo' => 1];
	protected static $damage = [5,20];
	protected static $range = [0,100];
	protected static $use_fixed_accuracy = false;

	protected static $animation = Model_Combat_Weapon::MCW_ANIMATION_SHOT_AMMO;

	protected static $accuracy_downscale = 0.5;

	// INI, ATK, DEF, ACC
	protected static $effects = [1,0,0,0];
}	