<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Machete3 extends Model_Combat_Weapons_Close implements Interface_Static {

	protected static $static_info = Array(
			'name' => 'Kosmische Machete',
			'icon' => 'machete3',
			'description' => 'Diese Machete ist nicht einfach scharf - sie ist kosmisch! Die Klinge besteht aus gehärtetem Meteoritenstahl und ist schärfer als Tods Sense. Notfalls kannst du damit sogar Atome spalten, durch Zombies geht die Klinge wie durch Luft.',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_FIGHT,
	);

	protected static $weight = 10;
	protected static $essential = true;

	protected static $damage = [10,13];
	protected static $energy = 3;
	protected static $max_range = 2;

	protected static $animation = Model_Combat_Weapon::MCW_ANIMATION_SLASH_MULTI;
}	