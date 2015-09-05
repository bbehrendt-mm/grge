<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Rifle extends Model_Combat_Weapons_Ammo implements Interface_Static {

	protected static $static_info = Array(
			'name' => 'Dein altes Sturmgewehr',
			'icon' => 'rifle',
			'description' => 'Es hat dich bisher gut duch die Zombieapokalypse gebracht, es wird dich auch weiter gut durchbringen. Zumindest, solange dir die Munition nicht ausgeht.',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_FIGHT,
	);
	
	protected static $weight = 15;
	protected static $essential = true;

	protected static $ammo = ['Model_Items_Ammo' => 1];
	protected static $damage = [15,20];
	protected static $range = [1,100];
	protected static $accuracy = 1;
	protected static $use_fixed_accuracy = false;
	protected static $aoe = true;

	protected static $animation = Model_Combat_Weapon::MCW_ANIMATION_SHOT_AMMO;

	protected static $accuracy_downscale = 0.75;

	// INI, ATK, DEF, ACC
	protected static $effects = [8,0,0,0];

	
	public function drop_dead() {
		return null;
	}
}	