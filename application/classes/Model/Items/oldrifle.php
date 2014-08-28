<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Oldrifle extends Model_Battle_Weapon implements Interface_Static {

	protected static $static_info = Array(
			'name' => 'Gewehr',
			'icon' => 'trifle',
			'description' => 'Ein gutes, altmodisches Gewehr - es ist nach wie vor schussbereit und sehr gut geeignet, einem Zombie den Kopf wegzublasen. Außerdem scheint man es mit der gleichen Munition wie eine Pistole laden zu können. Na, so ein Glück aber auch!',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_FIGHT,
	);
	
	protected static $weight = 15;
	protected static $essential = true;
	
	public static $range = Array(5,100);
	protected static $damage = Array(5,20);
	public static $damage_type = Model_Battle_Weapon::MBW_DMG_IMPACT;
	protected static $ammo = 'Model_Items_Ammo';
	public static $accuracy = 2;
	public static $accuracy_type = Model_Battle_Weapon::MBW_ACC_LINEAR_DISTANCE;
	public static $durability = 1;
	public static $bounce = 0;
	public static $reload_time = 3;
	public static $lock_type = Model_Battle_Weapon::MBW_LCK_WEAPON;
	public static $energy_cost = 0;
	
	public function drop_dead() {
		return null;
	}
}	