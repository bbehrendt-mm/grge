<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Miniknife extends Model_Combat_Weapons_Close implements Interface_Static {

	protected static $static_info = Array(
			'name' => 'Lächerliches Taschenmesser der Männlichkeit',
			'icon' => 'miniknife',
			'description' => 'Wenn alles andere fehlschlägt kannst du die Zombies immernoch mit diesem Taschenmesser .... zum Lachen bringen.',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_FIGHT,
	);

	protected static $essential = true;
	protected static $weight = 0;

    protected static $damage = [1,2];
    protected static $energy = 1;
    protected static $max_range = 0.5;

	protected static $animation = Model_Combat_Weapon::MCW_ANIMATION_SLASH;
	//public static $reload_time = 0;
	
	public function drop($silent = false) {
		/** @global Model_Player $player */
		global $player;

        if (!$silent) $player->log()->add(new Model_Log_Types_Text(null, null, 'Du fühlst dich ohne dein Taschenmesser ziemlich nackt ... du solltest es wirklich nicht einfach ablegen!'));
		return false;
	}
	
	public function drop_dead() {
		return null;
	}
}	