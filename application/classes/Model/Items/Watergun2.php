<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Watergun2 extends Model_Items_Abstract_Wbgun {
	
	protected static $static_info = Array(
			'name' => 'Aquablaster XL',
			'icon' => 'watergun2',
			'description' => 'Die Aquablaster XL ist die militärische Variante der Aquablaster XS. Durch das zusätzliche Hochleistungsprühsystem handelt es sich hierbei um eine tödliche Waffe (insbesondere für Zombies). Falls gerade keine Zombie-Apokalypse stattfindet, kann man sie auch zur Auflösung lästiger Demonstationen verwenden.',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_FIGHT,
	);

	protected static $weight = 7;
	protected static $essential = true;
	
	protected static $capacity = 4;
	public static $range = Array(0,15);
	protected static $damage = Array(15,20);
	public static $damage_type = Model_Battle_Weapon::MBW_DMG_AREA;
	protected static $custom_icon = "water_variant";
	protected static $ammo = 'custom';
	public static $accuracy = 3;
	public static $accuracy_type = Model_Battle_Weapon::MBW_ACC_LINEAR_DISTANCE;
	public static $durability = 1;
	public static $bounce = 0;
	public static $reload_time = 0;
	public static $lock_type = Model_Battle_Weapon::MBW_LCK_WEAPON;
	public static $energy_cost = 0;

}	