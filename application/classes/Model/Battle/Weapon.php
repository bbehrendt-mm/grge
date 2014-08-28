<?php defined('SYSPATH') OR die('No direct access allowed.');

abstract class Model_Battle_Weapon extends Model_Items_Abstract_Item {
	
	const MBW_ACC_STATIC = 1;
	const MBW_ACC_LINEAR_DISTANCE = 2;
	
	const MBW_DMG_IMPACT = 1;
	const MBW_DMG_AREA = 2;
	const MBW_DMG_SCATTER = 3;
	
	const MBW_LCK_WEAPON = 1;
	const MBW_LCK_PLAYER = 2;
	
	/**
	 * Array depicting minimal range (Fst) and maximal range (Snd)
	 * @var array
	 */
	public static $range = Array(0,100);
	
	/**
	 * number: Fixed damage;
	 * array: Damage range;
	 * @var mixed
	 */
	protected static $damage = 1;
	
	/**
	 * Damage type
	 * @var number
	 */
	public static $damage_type = Model_Battle_Weapon::MBW_DMG_IMPACT;
	
	/**
	 * Null: No ammo;
	 * String: Ammo class, or "self" to self-cosume;
	 * Array: Contains string classes
	 * @var mixed
	 */
	protected static $ammo = null;
	
	/**
	 * Accuracy
	 * @var number
	 */
	public static $accuracy = 1;

	public static $accuracy_type = Model_Battle_Weapon::MBW_ACC_LINEAR_DISTANCE;
	
	/**
	 * Durability (between 0 and 1)
	 * @var number
	 */
	public static $durability = 1;
	
	/**
	 * Opponent bounce
	 * @var int
	 */
	public static $bounce = 0;
	
	protected static $custom_icon = "";
	
	public static function custom_ammo_icon() {
		$i = static::$custom_icon;
		return "/application/assets/icons/items/{$i}.gif";
	} 
	
	public static $reload_time = 0;
	
	public static $lock_type = Model_Battle_Weapon::MBW_LCK_WEAPON;
	
	public $lock;
	
	public static $energy_cost = 0;
	
	public static function damage() {
		if (is_numeric(static::$damage)) return Array(static::$damage,static::$damage);
		else return static::$damage;
	}
	
	public static function ammo() {
		if (!static::$ammo) return Array();
		elseif (is_string(static::$ammo)) return Array(static::$ammo => 1);
		else return static::$ammo;
	}
}	