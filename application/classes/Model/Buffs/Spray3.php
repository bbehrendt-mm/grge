<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Buffs_Spray3 extends Model_Buffs_Abstract_Buff {

    protected static $name = 'Regenerations-Effekt';
    protected static $icon = 'spray/spray3';
    protected static $bid = 'spray3';
    protected static $desc = 'Du fühlst, wie deine Gesundheit langsam zurückkehrt - vielleicht solltest du noch so ein Spray einsetzen?';

    public function merge(Model_Buffs_Abstract_Buff $newclass): void {
        $this->lifetime += $newclass->lifetime;
    }

    protected function get_effects(): array { return [
        Model_Status::MS_STAT_HEALTH => Array(
            Model_Buffs_Abstract_Buff::MB_RAISE_ACC =>    2,
            Model_Buffs_Abstract_Buff::MB_DROP_ACC  =>    0,
            Model_Buffs_Abstract_Buff::MB_RAISE_PRC =>  0.5,
            Model_Buffs_Abstract_Buff::MB_DROP_PRC  => -0.5,
        ),
    ]; }
}
