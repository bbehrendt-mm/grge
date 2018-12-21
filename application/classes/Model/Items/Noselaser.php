<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Noselaser extends Model_Combat_Weapons_Ammo implements Interface_Static {

	protected static $static_info = Array(
			'name' => 'Rudolph\'s Nasenlaser',
			'icon' => 'nose',
			'description' => '',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_FIGHT,
	);

	protected static $weight = 0;

	protected static $damage = [10,30];
	protected static $range = [10,90];
	protected static $accuracy = 0.95;
	protected static $use_fixed_accuracy = false;
	protected static $aoe = true;

	protected static $animation = Model_Combat_Weapon::MCW_ANIMATION_SHOT_RLASER;
}	