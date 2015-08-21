<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Pepperspray extends Model_Combat_Weapons_Close implements Interface_Static {

	protected static $static_info = Array(
			'name' => 'Pfefferspray',
			'icon' => 'pepperspray',
			'description' => 'Dieses Pfefferspray ist eigentlich für Bären gedacht - hilft aber auch super gegen Zombies, Männer und andere hirntote Gestalten.',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_FIGHT,
	);

	protected static $weight = 2;
	protected static $essential = true;

    protected static $damage = [1,10];
    protected static $energy = 1;
    protected static $max_range = 7;

	protected static $animation = Model_Combat_Weapon::MCW_ANIMATION_PUNCH;

    //public static $reload_time = 1;
	
	public function drop($silent = false) {
		/** @global Model_Player $player */
		global $player;

		if (!$silent) $player->log()->add(new Model_Log_Types_Text(null, null, 'Bist du verrückt? Womit willst du dich wehren, wenn dir einer deiner Mitverdammten ein Kompliment über dein Aussehen machen möchte?'));
		return false;
	}
	
	public function drop_dead() {
		return null;
	}
}	