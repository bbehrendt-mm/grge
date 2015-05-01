<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Nrarifle extends Model_Battle_Weapon implements Interface_Static {

	protected static $static_info = Array(
			'name' => 'Sturmgewehr',
			'icon' => 'rifle_nra',
			'description' => 'Dieses Sturmgewehr erhält jeder, der eine lebenslange NRA-Mitgliedschaft abschließt. Achtung: Darf nur gegen Kriminelle, Auslänger, Nicht-Christen und Liberale verwendet werden.',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_FIGHT,
	);
	
	protected static $weight = 13;
	
	public static $range = Array(1,90);
	protected static $damage = Array(5,20);
	public static $damage_type = Model_Battle_Weapon::MBW_DMG_SCATTER;
	protected static $ammo = 'Model_Items_Ammo';
	public static $accuracy = 5;
	public static $accuracy_type = Model_Battle_Weapon::MBW_ACC_LINEAR_DISTANCE;
	public static $durability = 1;
	public static $bounce = 0;
	public static $reload_time = 0;
	public static $lock_type = Model_Battle_Weapon::MBW_LCK_WEAPON;
	public static $energy_cost = 0;
}	