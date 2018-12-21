<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Crossbow extends Model_Combat_Weapons_Ammo implements Interface_Static {

	protected static $static_info = Array(
			'name' => 'Provisorische Armbrust',
			'icon' => 'crossbow',
			'description' => 'Diese nicht sonderlich stabil aussehende Armbrust kann dein Retter in der Not werden, wenn dir mal wieder die Batterien ausgegangen sind. Immerhin kannst du ihre Bolzen an deiner Werkbank selbst fertigen.',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_FIGHT,
	);

	protected static $weight = 4;

	protected static $animation = Model_Combat_Weapon::MCW_ANIMATION_SHOT_BOLT;

	protected static $ammo = ['Model_Items_Bolts' => 1];
	protected static $damage = [4,8];
	protected static $range = [5,50];
	protected static $accuracy = 0.8;
	protected static $use_fixed_accuracy = false;

	// INI, ATK, DEF, ACC
	protected static $effects = [-1,0,0,0];
}	