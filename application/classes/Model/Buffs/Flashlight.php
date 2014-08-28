<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Buffs_Flashlight extends Model_Buffs_Abstract_Passive {
	
	protected static $name = 'Erleuchtung';
	protected static $icon = 'flashlight';
	protected static $desc = 'Du hast eine Taschenlampe bei dir, die dir beim Suchen nach Gegenständen hilft. Der nächtliche Fund-Malus wird negiert, tagsüber findest du in allen geschlossenen Ruinen außerdem schneller neue Gegenstände.';
	protected static $bid = 'flashlight';
	
	protected $effects = Array();

	protected function activator() {
        foreach ($this->assoc_player->inventory()->get('Model_Items_Flashlight') as $flashlight)
            /** @var $flashlight Model_Items_Flashlight */
            if ($flashlight->active()) return true;
        return false;
	}
}
