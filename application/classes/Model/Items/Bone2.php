<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Bone2 extends Model_Battle_Weapon implements Interface_Static {
	
	protected static $static_info = Array(
			'name' => 'Schädel',
			'icon' => 'bone2',
			'description' => 'To be, or not to be... wobei die Frage eher ist: "Warum zur Hölle schleppst du einen Schädel mit dir rum?". Du könntest dich natürlich damit rausreden, dass du das Ding auf Zombies werfen willst...',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_FIGHT,
	);
	
	protected static $weight = 5;

	public static $range = Array(1,20);
	protected static $damage = Array(1,2);
	public static $damage_type = Model_Battle_Weapon::MBW_DMG_IMPACT;
	protected static $ammo = 'self';
	public static $accuracy = 0.3;
	public static $accuracy_type = Model_Battle_Weapon::MBW_ACC_STATIC;
	public static $durability = 1;
	public static $bounce = 1;
	public static $reload_time = 1;
	public static $lock_type = Model_Battle_Weapon::MBW_LCK_PLAYER;
	public static $energy_cost = 1;
}	