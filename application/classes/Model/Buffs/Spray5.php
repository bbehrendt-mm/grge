<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Buffs_Spray5 extends Model_Buffs_Abstract_Buff {

    protected static $name = 'Nachtsicht';
    protected static $icon = 'spray/spray5';
    protected static $bid = 'spray5';
    protected static $desc = 'Mit dieser Sehkraft wird dir kein Gegenstand entgehen!';

    public function merge(Model_Buffs_Abstract_Buff $newclass): void {
        $this->lifetime += $newclass->lifetime;
    }

    protected function get_effects(): array {
        $day = Tool_Scripts::get_timeofday() == 'day';
        $night = in_array(Tool_Scripts::get_timeofday(), ['night','snowynight']);

        return [
            Model_Status::MS_CHAR_ITEM_SPAWNRATE => Array(
                Model_Buffs_Abstract_Buff::MB_RAISE_ACC => $night ? 2 : 0,
                Model_Buffs_Abstract_Buff::MB_DROP_ACC  => $day ?  1 : 0,
                Model_Buffs_Abstract_Buff::MB_RAISE_PRC => $day ? -1 : 0,
                Model_Buffs_Abstract_Buff::MB_DROP_PRC  => $day ?  1 : 0,
            ),
        ];
    }
}
