<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Splintergun extends Model_Battle_Weapon implements Interface_Static {

	protected static $static_info = Array(
			'name' => 'Selbstgebauter Splitterwerfer',
			'icon' => 'splintergun',
			'description' => 'Auf den ersten Blick sieht es aus wie ein Batteriewerfer - allerdings ist die Bauweise etwas kompakter. Dieser Splitterwerfer lässt sich mit Splitterkugeln laden. Er funktioniert ähnlich wie ein Batteriewerfer, kann aber bei richtiger Handhabung wesentlich mehr Schaden anrichten. Durch die explosive Wirkung der Splitterkugeln ist er eher für große Zombiemengen geeignet.',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_FIGHT,
	);
	
	protected static $weight = 5;

	public static $range = Array(1,20);
	protected static $damage = Array(10,20);
	public static $damage_type = Model_Battle_Weapon::MBW_DMG_SCATTER;
	protected static $ammo = 'Model_Items_Splinter';
	public static $accuracy = 0.8;
	public static $accuracy_type = Model_Battle_Weapon::MBW_ACC_LINEAR_DISTANCE;
	public static $durability = 1;
	public static $bounce = 0;
	public static $reload_time = 3;
	public static $lock_type = Model_Battle_Weapon::MBW_LCK_WEAPON;
	public static $energy_cost = 0;
}	