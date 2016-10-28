<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Buffs_Scarecrow extends Model_Buffs_Abstract_Passive {
	
	protected static $name = 'Grausame Vogelscheuche';
	protected static $icon = 'scarecrow';
	protected static $desc = 'Wenn dieses furchtbare Ding dich die ganze Zeit anglotzt ist es kaum möglich, zu entspannen. Bis Halloween vorbei ist wirst du auf jeden Fall keinen geruhsamen Schlaf mehr haben...';
	protected static $bid = 'scarecrow';
	
	protected $effects = Array(
			Model_Status::MS_STAT_SLEEPY => Array(
					Model_Buffs_Abstract_Buff::MB_RAISE_ACC => 0,
					Model_Buffs_Abstract_Buff::MB_DROP_ACC => 0,
					Model_Buffs_Abstract_Buff::MB_RAISE_PRC => -0.1,
					Model_Buffs_Abstract_Buff::MB_DROP_PRC => 0,
			),
	);

    protected function activator() {
        $r = false;
        foreach (Tool_Scripts::at_location($this->assoc_player->location_class(), false, true) as $npc)
            if (Tool_System::instance_of($npc, Model_NPC_Event_Scarecrow::cls())) {
                $r = true;

                break;
            }
        return $r;
    }
}
