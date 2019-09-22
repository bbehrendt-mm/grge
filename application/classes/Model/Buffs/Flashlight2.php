<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Buffs_Flashlight2 extends Model_Buffs_Abstract_Passive {
	
	protected static $name = 'Erleuchtung';
	protected static $icon = 'flashlight2';
	protected static $desc = 'Einer deiner Freunde erleuchtet diesen Ort mit einer Taschenlampe. Nachts und in geschlossenen Räumen findest du so leichter neue Gegenstände.';
	protected static $bid = 'flashlight2';

    protected function get_effects(): array {
        if (!$this->assoc_player) return [];

        $is_night = in_array(Tool_Scripts::get_timeofday(), ['night','snowynight']);
        $is_inside = !$this->assoc_player->location()->is_outside();

        $def = 0;
        if (!$is_night && $is_inside) $def = 0.1;
        elseif ($is_night) $def = 0.25;

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
        if (!$this->assoc_player) return false;

        if ($this->associated_to_player()) foreach ($this->assoc_player->inventory()->get(Model_Items_Flashlight::cls()) as $flashlight)
            /** @var $flashlight Model_Items_Flashlight */
            if ($flashlight->active()) return false;


        foreach (Tool_Scripts::at_location( $this->assoc_player->location_class(), true, false ) as $p)
            foreach ($p->inventory()->get(Model_Items_Flashlight::cls()) as $flashlight)
                /** @var $flashlight Model_Items_Flashlight */
                if ($flashlight->active()) return true;

        return false;
	}
}
