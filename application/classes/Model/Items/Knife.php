<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Knife extends Model_Combat_Weapons_Close implements Interface_Static {

	protected static $static_info = Array(
			'name' => 'Scharfes Messer',
			'icon' => 'knife',
			'description' => 'Mit einem scharfen Messer in der Tasche bist du für alles gerüstet: Plötzliche Zombieangriffe, spontane Kochduelle, überraschende RTL-Interviews! Du solltest wirklich nie ohne Messer aus dem Haus gehen.... es sei denn, du hast eine Machete.',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_FIGHT,
	);

	protected static $weight = 10;
	protected static $essential = true;

	protected static $damage = [2,3];
	protected static $energy = 2;

	protected static $animation = Model_Combat_Weapon::MCW_ANIMATION_SLASH;

	// INI, ATK, DEF, ACC
	protected static $effects = [1,0,0,0];
}	