<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Buffs_Driver extends Model_Buffs_Abstract_Fragile {

    protected static $name = 'Fahrer';
    protected static $desc = 'Du fährst das Wohnmobil - halte also immer ein Auge auf der Straße und vermeide Übermüdung oder extensiven Alkoholkonsum. Oder willst du dich und deine Mitfahrer umbringen?';
    protected static $icon = 'driver';
    protected static $alt_id = 'driver';

    protected static $allow_npc_assoc = false;

    protected static $abortable = false;

    protected function get_effects(): array { return [
        Model_Status::MS_STAT_ENERGY => Array(
            Model_Buffs_Abstract_Buff::MB_RAISE_ACC => 0,
            Model_Buffs_Abstract_Buff::MB_DROP_ACC => 0.2,
            Model_Buffs_Abstract_Buff::MB_RAISE_PRC => 0,
            Model_Buffs_Abstract_Buff::MB_DROP_PRC => 0,
        ),
        Model_Status::MS_STAT_SLEEPY => Array(
            Model_Buffs_Abstract_Buff::MB_RAISE_ACC => 0,
            Model_Buffs_Abstract_Buff::MB_DROP_ACC => 0.4,
            Model_Buffs_Abstract_Buff::MB_RAISE_PRC => 0,
            Model_Buffs_Abstract_Buff::MB_DROP_PRC => 0,
        )
    ]; }

    protected function action_on_complete() {}
}