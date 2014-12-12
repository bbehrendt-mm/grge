<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Batgunsplat extends Model_Battle_Weapon implements Interface_Static {

	protected static $static_info = Array(
			'name' => 'Zerstörer',
			'icon' => 'batgun_splat',
			'description' => 'Der Zerstörer verfügt über einen Hochdruck-Kompressor, um eine Batterie besonder effektiv zu verschießen. Unglücklicherweise halten Batterien diesem Druck nicht sonderlich gut stand, sodass der Zerstörer eher Metallsplitter als komplette Batterien verschießt. Diese richten zwar gewaltigen Schaden an, haben aber nicht unbedingt eine sonderlich große Reichweite. Außerdem wird dich der Rückstoß von den Füßen reissen, sodass du für eine kurze Zeit wehrlos bist.',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_FIGHT,
	);

	protected static $weight = 12;

	public static $range = Array(0,10);
	protected static $damage = Array(20,50);
	public static $damage_type = Model_Battle_Weapon::MBW_DMG_AREA;
	protected static $ammo = 'Model_Items_Battery';
	public static $accuracy = 0.9;
	public static $accuracy_type = Model_Battle_Weapon::MBW_ACC_STATIC;
	public static $durability = 1;
	public static $bounce = 0;
	public static $reload_time = 5;
	public static $lock_type = Model_Battle_Weapon::MBW_LCK_PLAYER;
	public static $energy_cost = 5;
}	