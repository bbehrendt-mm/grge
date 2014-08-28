<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Bone extends Model_Battle_Weapon implements Interface_Static {

	protected static $static_info = Array(
			'name' => 'Knochen',
			'icon' => 'bone1',
			'description' => 'Häng dir einen Mammut-Mantel um und zieh den Lendenschurz stramm - dieses Accessoire komplettiert deinen stylischen Neandertal-Look. Wenn du Ärger mit einem Zombie hast, knall ihm einfach dieses Ding über die Rübe und zieh ihn dann in deine Höhle.',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_FIGHT,
	);
	
	protected static $weight = 5;

	public static $range = Array(0,0);
	protected static $damage = Array(1,3);
	public static $damage_type = Model_Battle_Weapon::MBW_DMG_IMPACT;
	protected static $ammo = null;
	public static $accuracy = 1;
	public static $accuracy_type = Model_Battle_Weapon::MBW_ACC_STATIC;
	public static $durability = 0.3;
	public static $bounce = 0;
	public static $reload_time = 0;
	public static $lock_type = Model_Battle_Weapon::MBW_LCK_PLAYER;
	public static $energy_cost = 2;
}	