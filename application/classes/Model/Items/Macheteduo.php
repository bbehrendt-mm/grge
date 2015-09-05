<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Macheteduo extends Model_Combat_Weapons_Close implements Interface_Static {

	protected static $static_info = Array(
			'name' => '2 Macheten',
			'icon' => 'machete_dual',
			'description' => 'Es gibt nichts cooleres als mit zwei Macheten in der Hand wie ein Samurai durch Zombiehorden zu pflügen. Durch die ganze abgefahrene Choreographie verbrauchst du zwar einiges an Energie, aber das ist es wert!',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_FIGHT,
	);

	protected static $weight = 20;
	protected static $essential = true;

	protected static $damage = [9,20];
	protected static $energy = 10;
	protected static $max_range = 3;

	protected static $animation = Model_Combat_Weapon::MCW_ANIMATION_SLASH_MULTI;

	// INI, ATK, DEF, ACC
	protected static $effects = [4,0,4,0];
}	