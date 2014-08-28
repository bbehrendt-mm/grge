<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Batgun2 extends Model_Battle_Weapon implements Interface_Static {

	protected static $static_info = Array(
			'name' => 'Batteriewerfer mit verb. Ladevorrichtung',
			'icon' => 'batgun2',
			'description' => 'In mühevoller Handarbeit hast du diesem Batteriewerfer eine selbst entworfene, neue Ladevorrichtung verpasst. Eigentlich solltest du das Teil von nun an "Batterie-Maschinenwerfer" nennen ...',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_FIGHT,
	);

	protected static $weight = 10;

	public static $range = Array(1,60);
	protected static $damage = Array(5,10);
	public static $damage_type = Model_Battle_Weapon::MBW_DMG_IMPACT;
	protected static $ammo = 'Model_Items_Battery';
	public static $accuracy = 0.7;
	public static $accuracy_type = Model_Battle_Weapon::MBW_ACC_LINEAR_DISTANCE;
	public static $durability = 1;
	public static $bounce = 0;
	public static $reload_time = 1;
	public static $lock_type = Model_Battle_Weapon::MBW_LCK_WEAPON;
	public static $energy_cost = 0;
}	