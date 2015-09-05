<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Oldrifle extends Model_Combat_Weapons_Ammo implements Interface_Static {

	protected static $static_info = Array(
			'name' => 'Gewehr',
			'icon' => 'trifle',
			'description' => 'Ein gutes, altmodisches Gewehr - es ist nach wie vor schussbereit und sehr gut geeignet, einem Zombie den Kopf wegzublasen. Außerdem scheint man es mit der gleichen Munition wie eine Pistole laden zu können. Na, so ein Glück aber auch!',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_FIGHT,
	);

	protected static $weight = 15;
	protected static $essential = true;

	protected static $ammo = ['Model_Items_Ammo' => 1];
	protected static $damage = [5,20];
	protected static $range = [5,100];
	protected static $accuracy = 1;
	protected static $use_fixed_accuracy = false;
	protected static $aoe = false;

	protected static $animation = Model_Combat_Weapon::MCW_ANIMATION_SHOT_AMMO;

	protected static $accuracy_downscale = 0.5;
	
	public function drop_dead() {
		return null;
	}
}	