<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Buffs_Event_Rudolph extends Model_Buffs_Abstract_Passive {
	
	protected static $name = 'Nasaler Scheinwerfer';
	protected static $icon = 'rudolph';
	protected static $desc = 'Es gibt nichts weihnachtlichers, als einen verfallenen Weihnachtsmarkt im Schein der roten Nase eines sturzbetrunkenen Rentiers nach Gegenständen zu durchwühlen. Deine Fundchancen sind stak erhöht.';
	protected static $bid = 'rudolph';

	protected function activator(): bool
    {
	    if (!$this->associated_to_player()) return false;

        foreach (Tool_Scripts::at_location($this->assoc_player->location_class(), false, true) as $npc)
            /** @var Model_NPC_Event_Rudolph $npc */
            /** @noinspection NotOptimalIfConditionsInspection */
            if (Tool_System::instance_of($npc, Model_NPC_Event_Rudolph::cls()) && $npc->dispense_light())
                return true;

        return false;
	}
}
