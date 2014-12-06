<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Buffs_Home2 extends Model_Buffs_Abstract_Buff {
	
	protected static $name = 'Insel des Schreckens';
	protected static $icon = 'home2';
	protected static $desc = 'Diese gruselige Umgebung stört deine empfindliche Darmflora... dein Wasser- und Nahrungsverbrauch steigt.';
	protected static $bid = 'home';
    protected static $dominance = Model_Buffs_Abstract_Buff::MBR_PARTIALLY_DOMINANT;
	
	protected $effects = Array(		
				Model_Player::MP_STAT_HUNGER => Array(
					Model_Buffs_Abstract_Buff::MB_RAISE_ACC => 0,
					Model_Buffs_Abstract_Buff::MB_DROP_ACC => 0,
					Model_Buffs_Abstract_Buff::MB_RAISE_PRC => 0,
					Model_Buffs_Abstract_Buff::MB_DROP_PRC => 0.9,
				),				
				Model_Player::MP_STAT_THIRST => Array(
						Model_Buffs_Abstract_Buff::MB_RAISE_ACC => 0,
						Model_Buffs_Abstract_Buff::MB_DROP_ACC => 0,
						Model_Buffs_Abstract_Buff::MB_RAISE_PRC => 0,
						Model_Buffs_Abstract_Buff::MB_DROP_PRC => 0.6,
				),				
			);
}
