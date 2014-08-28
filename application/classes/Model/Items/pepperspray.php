<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Pepperspray extends Model_Battle_Weapon implements Interface_Static {

	protected static $static_info = Array(
			'name' => 'Pfefferspray',
			'icon' => 'pepperspray',
			'description' => 'Dieses Pfefferspray ist eigentlich für Bären gedacht - hilft aber auch super gegen Zombies, Männer und andere hirntote Gestalten.',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_FIGHT,
	);

	protected static $weight = 2;
	protected static $essential = true;

	public static $range = Array(0,7);
	protected static $damage = Array(1,10);
	public static $damage_type = Model_Battle_Weapon::MBW_DMG_SCATTER;
	protected static $ammo = null;
	public static $accuracy = 1;
	public static $accuracy_type = Model_Battle_Weapon::MBW_ACC_STATIC;
	public static $durability = 1;
	public static $bounce = 1;
	public static $reload_time = 1;
	public static $lock_type = Model_Battle_Weapon::MBW_LCK_PLAYER;
	public static $energy_cost = 1;

	
	public function drop() {
		global $game, $player;
		$player->log()->add(new Model_Log_Types_Text(null, null, 'Bist du verrückt? Womit willst du dich wehren, wenn dir einer deiner Mitverdammten ein Kompliment über dein Aussehen machen möchte?'));
		return false;
	}
	
	public function drop_dead() {
		return null;
	}
}	