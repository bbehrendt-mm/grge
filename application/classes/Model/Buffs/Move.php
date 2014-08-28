<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Buffs_Move extends Model_Buffs_Abstract_Buff {

    protected static $name = 'Marsch';
    protected static $icon = 'move';
    protected static $bid = 'move';
    protected static $desc = 'Ein bisschen Laufen ist für einen Pfadfinder deiner Klasse kein Problem. Du musst keine Energie mehr zum Laufen aufbringen.';
	
	protected $effects = Array(
        Model_Player::MP_CHAR_DISTANCING => Array(
                Model_Buffs_Abstract_Buff::MB_RAISE_ACC => 0,
                Model_Buffs_Abstract_Buff::MB_DROP_ACC => 1,
                Model_Buffs_Abstract_Buff::MB_RAISE_PRC => 0,
                Model_Buffs_Abstract_Buff::MB_DROP_PRC => 0,
        )
	);
}
