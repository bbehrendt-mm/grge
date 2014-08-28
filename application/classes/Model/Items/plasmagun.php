<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Plasmagun extends Model_Battle_Weapon implements Interface_Static {

	protected static $static_info = Array(
			'name' => 'Experimentelle Plasmakanone',
			'icon' => 'plasmagun',
			'description' => 'Dieser Waffe liegt ein faszinierendes Konzept zu grunde: Was, wenn man die Energie einer Batterie als Waffe nutzen würde, anstatt einfach die Batterie zu verschießen? Die experimentelle Plasmakanone benutzt richtig abgefahrene Wissenschaft sowie eine Batterie, um auf Plasmatemperatur erhitzte Luftpartikel in Richtung Zombies zu katapultieren. Wo diese Waffe hinfeuert bleibt kein Stein mehr auf dem anderen... allerdings kannst du sie aufgrund der extremen Hitzeentwicklung nur einmal pro Kampf einsetzen - und auch nur gegen Zombies, die weit entfernt stehen.',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_FIGHT,
	);

	protected static $weight = 4;
	
	public static $range = Array(50,100);
	protected static $damage = Array(40,60);
	public static $damage_type = Model_Battle_Weapon::MBW_DMG_AREA;
	protected static $ammo = 'Model_Items_Battery';
	public static $accuracy = 1;
	public static $accuracy_type = Model_Battle_Weapon::MBW_ACC_STATIC;
	public static $durability = 1;
	public static $bounce = 0;
	public static $reload_time = 9999;
	public static $lock_type = Model_Battle_Weapon::MBW_LCK_WEAPON;
	public static $energy_cost = 0;
}	