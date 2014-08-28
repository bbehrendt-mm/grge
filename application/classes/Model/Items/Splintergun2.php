<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Splintergun2 extends Model_Battle_Weapon {

	protected static $static_info = Array(
			'name' => 'Splitterwerfer Mark II',
			'icon' => 'splintergun2',
			'description' => 'Dieser Splitterwerfer mit verbessertem Druckausgleichsregler, feinjustierter Zielautomatik und integriertem Fluxkompensator übertrifft alle Leistungsdaten des Basismodells um Längen. Außerdem erhöht er den Rambo-Faktor des Trägers sofort um 78.92%.',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_FIGHT,
	);
	
	protected static $weight = 8;

	public static $range = Array(1,30);
	protected static $damage = Array(15,30);
	public static $damage_type = Model_Battle_Weapon::MBW_DMG_SCATTER;
	protected static $ammo = 'Model_Items_Splinter';
	public static $accuracy = 0.9;
	public static $accuracy_type = Model_Battle_Weapon::MBW_ACC_LINEAR_DISTANCE;
	public static $durability = 1;
	public static $bounce = 0;
	public static $reload_time = 2;
	public static $lock_type = Model_Battle_Weapon::MBW_LCK_WEAPON;
	public static $energy_cost = 0;
}	