<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Buffs_Fatigue extends Model_Buffs_Abstract_Buff {
	
	protected static $name = 'Müdigkeit';
	protected static $icon = 'fatigue';
	protected static $bid = 'fatigue';
	protected static $visible = false;

    protected function get_effects(): array {
        $ft = $this->assoc_player->get_status()->get(Model_Status::MS_STAT_SLEEPY);
        return [
            Model_Status::MS_CHAR_ITEM_SPAWNRATE => Array(
                Model_Buffs_Abstract_Buff::MB_RAISE_ACC => ($ft >= 90) ? ( ($ft - 90) / 100 ) : 0,
                Model_Buffs_Abstract_Buff::MB_DROP_ACC => ($ft < 50) ? ( 1 - ($ft / 50) ) : 0,
                Model_Buffs_Abstract_Buff::MB_RAISE_PRC => 0,
                Model_Buffs_Abstract_Buff::MB_DROP_PRC => 0,
            ),
        ];
    }
}
