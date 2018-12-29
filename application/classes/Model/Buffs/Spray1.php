<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Buffs_Spray1 extends Model_Buffs_Abstract_Buff {

    protected static $name = 'Kampfspray-Effekt';
    protected static $icon = 'spray/spray1';
    protected static $bid = 'spray1';
    protected static $desc = 'Du hast dir einen guten Überblick über die Lage verschafft und kannst daher wesentlich effektiver kämpfen.';

    protected $stack = 1;

    public function merge(Model_Buffs_Abstract_Buff $newclass): void {
        $this->lifetime += ($newclass->lifetime / $this->stack);
        $this->stack++;
    }

    protected function get_effects(): array { return [
        Model_Status::MS_STAT_HEALTH => Array(
            Model_Buffs_Abstract_Buff::MB_RAISE_ACC => 0,
            Model_Buffs_Abstract_Buff::MB_DROP_ACC => 0.5,
            Model_Buffs_Abstract_Buff::MB_RAISE_PRC => 0,
            Model_Buffs_Abstract_Buff::MB_DROP_PRC => 0.1 * ((1 - $this->stack) ** 2),
        ),
        Model_Status::MS_CHAR_DAMAGE_MULTIPLIER => Array(
            Model_Buffs_Abstract_Buff::MB_RAISE_ACC => 0.2 * $this->stack,
            Model_Buffs_Abstract_Buff::MB_DROP_ACC => 0,
            Model_Buffs_Abstract_Buff::MB_RAISE_PRC => 0,
            Model_Buffs_Abstract_Buff::MB_DROP_PRC => 0,
        )
    ]; }
}
