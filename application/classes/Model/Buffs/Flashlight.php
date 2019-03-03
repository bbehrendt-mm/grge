<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Buffs_Flashlight extends Model_Buffs_Abstract_Passive {
	
	protected static $name = 'Erleuchtung';
	protected static $icon = 'flashlight';
	protected static $desc = 'Du hast eine Taschenlampe bei dir, die dir beim Suchen nach Gegenständen hilft. Der nächtliche Fund-Malus wird negiert, tagsüber findest du in allen geschlossenen Ruinen außerdem schneller neue Gegenstände.';
	protected static $bid = 'flashlight';

    protected function get_effects(): array {
        if (!$this->associated_to_player()) return [];

        $is_night = in_array(Tool_Scripts::get_timeofday(), ['night','snowynight']);
        $is_inside = !$this->assoc_player->location()->is_outside();

        $def = 0;
        if (!$is_night && $is_inside) $def = 0.2;
        elseif ($is_night) $def = 0.75;

        return [
            Model_Status::MS_CHAR_ITEM_SPAWNRATE => [
                Model_Buffs_Abstract_Buff::MB_RAISE_ACC => $def,
                Model_Buffs_Abstract_Buff::MB_DROP_ACC => 0,
                Model_Buffs_Abstract_Buff::MB_RAISE_PRC => 0,
                Model_Buffs_Abstract_Buff::MB_DROP_PRC => 0,
            ]
        ];
    }

	protected function activator(): bool
    {
        if (!$this->associated_to_player()) return false;

        foreach ($this->assoc_player->inventory()->get(Model_Items_Flashlight::cls()) as $flashlight)
            /** @var $flashlight Model_Items_Flashlight */
            if ($flashlight->active()) return true;
        return false;
	}
}
