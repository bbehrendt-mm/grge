<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Hacksaw extends Model_Combat_Weapons_Close implements Interface_Static {

	protected static $static_info = Array(
			'name' => 'Metallsäge',
			'icon' => 'hacksaw',
			'description' => 'Beim Anblick dieser Säge läuft dir ein kalter Schauer den Rücken herunter...',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_FIGHT,
	);

	protected static $weight = 5;

	protected static $damage = [4,9];
	protected static $energy = 8;
	protected static $max_range = 0.5;

	protected static $animation = Model_Combat_Weapon::MCW_ANIMATION_SLASH;
}	