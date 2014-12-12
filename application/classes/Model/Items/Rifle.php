<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Rifle extends Model_Battle_Weapon implements Interface_Static {

	protected static $static_info = Array(
			'name' => 'Dein altes Sturmgewehr',
			'icon' => 'rifle',
			'description' => 'Es hat dich bisher gut duch die Zombieapokalypse gebracht, es wird dich auch weiter gut durchbringen. Zumindest, solange dir die Munition nicht ausgeht.',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_FIGHT,
	);
	
	protected static $weight = 15;
	protected static $essential = true;
	
	public static $range = Array(1,100);
	protected static $damage = Array(15,20);
	public static $damage_type = Model_Battle_Weapon::MBW_DMG_SCATTER;
	protected static $ammo = 'Model_Items_Ammo';
	public static $accuracy = 6;
	public static $accuracy_type = Model_Battle_Weapon::MBW_ACC_LINEAR_DISTANCE;
	public static $durability = 1;
	public static $bounce = 0;
	public static $reload_time = 0;
	public static $lock_type = Model_Battle_Weapon::MBW_LCK_WEAPON;
	public static $energy_cost = 0;
	
	public function drop_dead() {
		return null;
	}
}	