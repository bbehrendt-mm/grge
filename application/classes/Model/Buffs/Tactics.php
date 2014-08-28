<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Buffs_Tactics extends Model_Buffs_Abstract_Buff {

    protected static $name = 'Taktik';
    protected static $icon = 'tactics';
    protected static $bid = 'tactics';
    protected static $desc = 'Du hast dir einen guten Überblick über die Lage verschafft und kannst daher wesentlich effektiver kämpfen.';
	
	protected $effects = Array(
        Model_Player::MP_CHAR_ACCURACY => Array(
                Model_Buffs_Abstract_Buff::MB_RAISE_ACC => 0.5,
                Model_Buffs_Abstract_Buff::MB_DROP_ACC => 0,
                Model_Buffs_Abstract_Buff::MB_RAISE_PRC => 0,
                Model_Buffs_Abstract_Buff::MB_DROP_PRC => 0,
        ),
        Model_Player::MP_CHAR_DAMAGE_MULTIPLIER => Array(
            Model_Buffs_Abstract_Buff::MB_RAISE_ACC => 0.5,
            Model_Buffs_Abstract_Buff::MB_DROP_ACC => 0,
            Model_Buffs_Abstract_Buff::MB_RAISE_PRC => 0,
            Model_Buffs_Abstract_Buff::MB_DROP_PRC => 0,
        ),
	);
}
