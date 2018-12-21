<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Machete2 extends Model_Combat_Weapons_Close implements Interface_Static {

	protected static $static_info = Array(
			'name' => 'Scharfe Machete',
			'icon' => 'machete2',
			'description' => 'Nachdem du den Rost abgeschliffen und die Klinge geschärft hast gleitet deine Machete nun wie Butter durch Zombiehorden.',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_FIGHT,
	);

	protected static $weight = 10;
	protected static $essential = true;

	protected static $damage = [5,12];
	protected static $energy = 3;

	protected static $animation = Model_Combat_Weapon::MCW_ANIMATION_SLASH;
}	