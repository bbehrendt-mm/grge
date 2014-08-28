<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Miniknife extends Model_Battle_Weapon implements Interface_Static {

	protected static $static_info = Array(
			'name' => 'Lächerliches Taschenmesser der Männlichkeit',
			'icon' => 'miniknife',
			'description' => 'Wenn alles andere fehlschlägt kannst du die Zombies immernoch mit diesem Taschenmesser .... zum Lachen bringen.',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_FIGHT,
	);

	protected static $essential = true;
	protected static $weight = 0;
	
	public static $range = Array(0,0);
	protected static $damage = Array(1,2);
	public static $damage_type = Model_Battle_Weapon::MBW_DMG_IMPACT;
	protected static $ammo = null;
	public static $accuracy = 1;
	public static $accuracy_type = Model_Battle_Weapon::MBW_ACC_STATIC;
	public static $durability = 1;
	public static $bounce = 0;
	public static $reload_time = 0;
	public static $lock_type = Model_Battle_Weapon::MBW_LCK_PLAYER;
	public static $energy_cost = 1;
	
	public function drop() {
		global $game, $player;
	
		$player->log()->add(new Model_Log_Types_Text(null, null, 'Du fühlst dich ohne dein Taschenmesser ziemlich nackt ... du solltest es wirklich nicht einfach ablegen!'));
		return false;
	}
	
	public function drop_dead() {
		return null;
	}
}	