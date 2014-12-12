<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Batgun4 extends Model_Battle_Weapon implements Interface_Static {

	protected static $static_info = Array(
			'name' => 'Batteriewerfer MK IV Prototyp',
			'icon' => 'batgun4',
			'description' => 'Dieses Gerät wurde kurz nach der Apokalypse vom Militär entwickelt. Die enorm hohe Abschussgeschwindigkeit des MK IV erlaubt maximale Präzision - wenn nötig kannst du damit einem Zombie auf 500m Entfernung den rechten Backenzahn herausschießen (inklusive dem Rest seines Gebisses). Ein hübscher Nebeneffekt dieser Feuerkraft ist die Tatsache, dass die Batterien beim Aufprall zerplatzen und wie Splittergranaten wirken.',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_FIGHT,
	);

	protected static $weight = 10;
	
	public static $range = Array(1,85);
	protected static $damage = Array(10,15);
	public static $damage_type = Model_Battle_Weapon::MBW_DMG_IMPACT;
	protected static $ammo = 'Model_Items_Battery';
	public static $accuracy = 0.9;
	public static $accuracy_type = Model_Battle_Weapon::MBW_ACC_LINEAR_DISTANCE;
	public static $durability = 1;
	public static $bounce = 0;
	public static $reload_time = 1;
	public static $lock_type = Model_Battle_Weapon::MBW_LCK_WEAPON;
	public static $energy_cost = 0;
}	