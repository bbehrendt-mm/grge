<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Buffs_Spray4 extends Model_Buffs_Abstract_Buff {

    protected static $name = 'Röntgenblick';
    protected static $icon = 'spray/spray4';
    protected static $bid = 'spray4';
    protected static $desc = 'Mit dieser Sehkraft wird dir kein Gegenstand entgehen!';

    public function merge(Model_Buffs_Abstract_Buff $newclass): void {
        $this->lifetime += $newclass->lifetime;
    }

    protected function get_effects(): array { return [
        Model_Status::MS_STAT_HEALTH => Array(
            Model_Buffs_Abstract_Buff::MB_RAISE_ACC =>    0,
            Model_Buffs_Abstract_Buff::MB_DROP_ACC  =>    0,
            Model_Buffs_Abstract_Buff::MB_RAISE_PRC =>    0,
            Model_Buffs_Abstract_Buff::MB_DROP_PRC  =>  0.5,
        ),
        Model_Status::MS_CHAR_ITEM_SPAWNRATE => Array(
            Model_Buffs_Abstract_Buff::MB_RAISE_ACC => 0.5,
            Model_Buffs_Abstract_Buff::MB_DROP_ACC  => 0,
            Model_Buffs_Abstract_Buff::MB_RAISE_PRC => 0,
            Model_Buffs_Abstract_Buff::MB_DROP_PRC  => 0,
        ),
    ]; }
}
