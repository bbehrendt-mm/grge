<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Phone extends Model_Battle_Weapon implements Interface_Static {

	protected static $static_info = Array(
			'name' => 'Windows Phone',
			'icon' => 'wphone',
			'description' => 'Wow, du hast ein Windows Phone gefunden - DAS Smartphone für Menschen, die sich auch einen Trabbi zum Preis eines Porsches andrehen lassen. Naja, mit diesem Teil kannst du zwar nicht wirklich angeben (oder ZombVival spielen), aber du kannst es zumindest auf einen Zombie werfen. Das ist doch auch was!',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_FIGHT,
	);
	
	protected static $weight = 4;
	
	public static $range = Array(1,33);
	protected static $damage = Array(5,15);
	public static $damage_type = Model_Battle_Weapon::MBW_DMG_IMPACT;
	protected static $ammo = 'self';
	public static $accuracy = 0.85;
	public static $accuracy_type = Model_Battle_Weapon::MBW_ACC_STATIC;
	public static $durability = 1;
	public static $bounce = 0;
	public static $reload_time = 1;
	public static $lock_type = Model_Battle_Weapon::MBW_LCK_PLAYER;
	public static $energy_cost = 3;

}	