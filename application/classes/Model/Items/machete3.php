<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Machete3 extends Model_Battle_Weapon implements Interface_Static {

	protected static $static_info = Array(
			'name' => 'Kosmische Machete',
			'icon' => 'machete3',
			'description' => 'Diese Machete ist nicht einfach scharf - sie ist kosmisch! Die Klinge besteht aus gehärtetem Meteoritenstahl und ist schärfer als Tods Sense. Notfalls kannst du damit sogar Atome spalten, durch Zombies geht die Klinge wie durch Luft.',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_FIGHT,
	);

	protected static $weight = 10;
	protected static $essential = true;
	
	public static $range = Array(0,2);
	protected static $damage = Array(10,13);
	public static $damage_type = Model_Battle_Weapon::MBW_DMG_AREA;
	protected static $ammo = null;
	public static $accuracy = 1;
	public static $accuracy_type = Model_Battle_Weapon::MBW_ACC_STATIC;
	public static $durability = 1;
	public static $bounce = 2;
	public static $reload_time = 1;
	public static $lock_type = Model_Battle_Weapon::MBW_LCK_PLAYER;
	public static $energy_cost = 3;
}	