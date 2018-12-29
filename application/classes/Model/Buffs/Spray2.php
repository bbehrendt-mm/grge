<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Buffs_Spray2 extends Model_Buffs_Abstract_Buff {

    protected static $name = 'Berserker-Effekt';
    protected static $icon = 'spray/spray2';
    protected static $bid = 'spray2';
    protected static $desc = 'Du verspürst das dringende Bedürfnis, jeden Zombie in deiner Umgebung zu einem blutigen Brei zu schlagen.';

    public function merge(Model_Buffs_Abstract_Buff $newclass): void {
        $this->lifetime += $newclass->lifetime;
    }

    protected function get_effects(): array { return [
        Model_Status::MS_STAT_HEALTH => Array(
            Model_Buffs_Abstract_Buff::MB_RAISE_ACC => 0,
            Model_Buffs_Abstract_Buff::MB_DROP_ACC => 0.5,
            Model_Buffs_Abstract_Buff::MB_RAISE_PRC => 0,
            Model_Buffs_Abstract_Buff::MB_DROP_PRC => 0.1,
        ),
        Model_Status::MS_CHAR_DAMAGE_RESISTANCE => Array(
            Model_Buffs_Abstract_Buff::MB_RAISE_ACC => 0.3,
            Model_Buffs_Abstract_Buff::MB_DROP_ACC => 0,
            Model_Buffs_Abstract_Buff::MB_RAISE_PRC => 0,
            Model_Buffs_Abstract_Buff::MB_DROP_PRC => 0,
        ),
        Model_Status::MS_CHAR_DAMAGE_MULTIPLIER => Array(
            Model_Buffs_Abstract_Buff::MB_RAISE_ACC => 4,
            Model_Buffs_Abstract_Buff::MB_DROP_ACC => 0,
            Model_Buffs_Abstract_Buff::MB_RAISE_PRC => 0,
            Model_Buffs_Abstract_Buff::MB_DROP_PRC => 0,
        )
    ]; }
}
