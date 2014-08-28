<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Hwaterbomb extends Model_Battle_Weapon implements Interface_Fillable, Interface_Static {

	protected static $static_info = Array(
			'name' => 'Weihasserbombe',
			'icon' => 'waterbombh',
			'description' => 'Sobald die Zombies in Wurfreichweite kommen kannst du ihnen mit diesem kleinen Geschenk die Tour vermiesen. Nichts ist effektiver gegen eine Gruppe Zombies als eine Wasserbombe - außer natürlich einer Weihwasserbombe!!',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_FIGHT,
	);
	
	protected static $weight = 10;
	
	public static $range = Array(1,24);
	protected static $damage = Array(25,60);
	public static $damage_type = Model_Battle_Weapon::MBW_DMG_AREA;
	protected static $ammo = 'self';
	public static $accuracy = 0.85;
	public static $accuracy_type = Model_Battle_Weapon::MBW_ACC_STATIC;
	public static $durability = 1;
	public static $bounce = 0;
	public static $reload_time = 1;
	public static $lock_type = Model_Battle_Weapon::MBW_LCK_PLAYER;
	public static $energy_cost = 1;
	
	public function interaction_fillfrom($id) {
		global $game, $player;
	
		$player->log()->add(new Model_Log_Types_Text(null, null, 'Diese Wasserbombe ist bereits gefüllt.'));
		return false;
	}
	
	public function interaction_fill($liquid_id) {
		global $game, $player;
	
		$player->log()->add(new Model_Log_Types_Text(null, null, 'Diese Wasserbombe ist bereits gefüllt.'));
		return false;
	}
	
	public function capacity() {
		return 1;
	}
	
	public function fillrate() {
		return 1;
	}
	
	public function drop_dead() {
		return new Model_Items_Waterbomb();
	}
}	