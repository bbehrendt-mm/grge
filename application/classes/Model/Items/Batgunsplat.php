<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Batgunsplat extends Model_Combat_Weapons_Ammo implements Interface_Static {

	protected static $static_info = Array(
			'name' => 'Zerstörer',
			'icon' => 'batgun_splat',
			'description' => 'Der Zerstörer verfügt über einen Hochdruck-Kompressor, um eine Batterie besonder effektiv zu verschießen. Unglücklicherweise halten Batterien diesem Druck nicht sonderlich gut stand, sodass der Zerstörer eher Metallsplitter als komplette Batterien verschießt. Diese richten zwar gewaltigen Schaden an, haben aber nicht unbedingt eine sonderlich große Reichweite. Außerdem wird dich der Rückstoß von den Füßen reissen, sodass du für eine kurze Zeit wehrlos bist.',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_FIGHT,
	);

	protected static $weight = 12;

	protected static $ammo = ['Model_Items_Battery' => 1];
	protected static $damage = [20,50];
	protected static $range = [0,10];
	protected static $accuracy = 0.9;
	protected static $aoe = true;

	protected static $animation = Model_Combat_Weapon::MCW_ANIMATION_SHOT_BAT;

	// INI, ATK, DEF, ACC
	protected static $effects = [-2,0,0,0];
}	