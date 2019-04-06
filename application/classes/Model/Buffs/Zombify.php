<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Buffs_Zombify extends Model_Buffs_Abstract_Buff {
	
	protected static $name = 'Zombifizierung';
	protected static $icon = 'zombify';
	protected static $bid = 'zombify';
	protected static $visible = false;

    protected function get_effects(): array {
        $zombify = $this->assoc_player->get_status()->get(Model_Status::MS_STAT_ZOMBIFY);

        if ($zombify == 0) return [];

        $health = $this->assoc_player->get_status()->get(Model_Status::MS_STAT_HEALTH);

        return [
            Model_Status::MS_STAT_HEALTH => [
                Model_Buffs_Abstract_Buff::MB_RAISE_ACC => 0,
                Model_Buffs_Abstract_Buff::MB_DROP_ACC => ($health > (100-$zombify)) ? 1 : 0,
                Model_Buffs_Abstract_Buff::MB_RAISE_PRC => -$zombify/100,
                Model_Buffs_Abstract_Buff::MB_DROP_PRC => 0,
            ],
            Model_Status::MS_STAT_ENERGY => [
                Model_Buffs_Abstract_Buff::MB_RAISE_ACC => 0,
                Model_Buffs_Abstract_Buff::MB_DROP_ACC => 0,
                Model_Buffs_Abstract_Buff::MB_RAISE_PRC => -$zombify/100,
                Model_Buffs_Abstract_Buff::MB_DROP_PRC => -$zombify/100,
            ],
            Model_Status::MS_STAT_HUNGER => [
                Model_Buffs_Abstract_Buff::MB_RAISE_ACC => 0,
                Model_Buffs_Abstract_Buff::MB_DROP_ACC => 0,
                Model_Buffs_Abstract_Buff::MB_RAISE_PRC => 0,
                Model_Buffs_Abstract_Buff::MB_DROP_PRC => $zombify/100,
            ],
            Model_Status::MS_STAT_THIRST => [
                Model_Buffs_Abstract_Buff::MB_RAISE_ACC => 0,
                Model_Buffs_Abstract_Buff::MB_DROP_ACC => 0,
                Model_Buffs_Abstract_Buff::MB_RAISE_PRC => 0,
                Model_Buffs_Abstract_Buff::MB_DROP_PRC => -$zombify/100,
            ],
            Model_Status::MS_STAT_SLEEPY => [
                Model_Buffs_Abstract_Buff::MB_RAISE_ACC => 0,
                Model_Buffs_Abstract_Buff::MB_DROP_ACC => 0,
                Model_Buffs_Abstract_Buff::MB_RAISE_PRC => 0,
                Model_Buffs_Abstract_Buff::MB_DROP_PRC => -$zombify/100,
            ],
            Model_Status::MS_STAT_ZOMBIFY => [
                Model_Buffs_Abstract_Buff::MB_RAISE_ACC => $zombify/500,
                Model_Buffs_Abstract_Buff::MB_DROP_ACC => 0,
                Model_Buffs_Abstract_Buff::MB_RAISE_PRC => 0,
                Model_Buffs_Abstract_Buff::MB_DROP_PRC => 0,
            ]
        ];
    }
}
