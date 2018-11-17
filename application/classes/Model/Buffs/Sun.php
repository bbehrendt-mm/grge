<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Buffs_Sun extends Model_Buffs_Abstract_Buff {
	
	protected static $name = 'Brennende Sonne';
	protected static $icon = 'sun';
	protected static $desc = 'Die Sonne strahlt gnadenlos und der Boden unter deinen Füßen glüht. Dein Wasserbedarf steigt.';
	protected static $bid = 'sun';

    protected function get_effects(): array { return [
        Model_Status::MS_STAT_THIRST => Array(
            Model_Buffs_Abstract_Buff::MB_RAISE_ACC => 0,
            Model_Buffs_Abstract_Buff::MB_DROP_ACC => 0,
            Model_Buffs_Abstract_Buff::MB_RAISE_PRC => 0,
            Model_Buffs_Abstract_Buff::MB_DROP_PRC => 0.3,
        )
    ]; }
}
