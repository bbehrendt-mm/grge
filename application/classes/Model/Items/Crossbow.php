<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Crossbow extends Model_Battle_Weapon implements Interface_Static {

	protected static $static_info = Array(
			'name' => 'Provisorische Armbrust',
			'icon' => 'crossbow',
			'description' => 'Diese nicht sonderlich stabil aussehende Armbrust kann dein Retter in der Not werden, wenn dir mal wieder die Batterien ausgegangen sind. Immerhin kannst du ihre Bolzen an deiner Werkbank selbst fertigen.',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_FIGHT,
	);

	protected static $weight = 4;
	
	public static $range = Array(5,50);
	protected static $damage = Array(4,8);
	public static $damage_type = Model_Battle_Weapon::MBW_DMG_IMPACT;
	protected static $ammo = 'Model_Items_Bolts';
	public static $accuracy = 0.8;
	public static $accuracy_type = Model_Battle_Weapon::MBW_ACC_LINEAR_DISTANCE;
	public static $durability = 0.95;
	public static $bounce = 0;
	public static $reload_time = 3;
	public static $lock_type = Model_Battle_Weapon::MBW_LCK_WEAPON;
	public static $energy_cost = 0;
}	