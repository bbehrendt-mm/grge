<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Buffs_Event_Rudolph extends Model_Buffs_Abstract_Passive {
	
	protected static $name = 'Nasaler Scheinwerfer';
	protected static $icon = 'rudolph';
	protected static $desc = 'Es gibt nichts weihnachtlichers, als einen verfallenen Weihnachtsmarkt im Schein der roten Nase eines sturzbetrunkenen Rentiers nach Gegenständen zu durchwühlen. Deine Fundchancen sind stak erhöht.';
	protected static $bid = 'rudolph';

    protected function get_effects(): array {
        if (!$this->associated()) return [];

        $is_night = in_array(Tool_Scripts::get_timeofday($this->assoc_player), ['night','snowynight']);
        $is_inside = !$this->assoc_player->location()->is_outside();

        $def = 0;
        if (!$is_night && $is_inside) $def = 0.1;
        elseif ($is_night) $def = 0.5;

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
	    if (!$this->associated()) return false;

        foreach (Tool_Scripts::at_location($this->assoc_player->location_class(), false, true) as $npc)
            /** @var Model_NPC_Event_Rudolph $npc */
            /** @noinspection NotOptimalIfConditionsInspection */
            if (Tool_System::instance_of($npc, Model_NPC_Event_Rudolph::cls()) && $npc->dispense_light())
                return true;

        return false;
	}
}
