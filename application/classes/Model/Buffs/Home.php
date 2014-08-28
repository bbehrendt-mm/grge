<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Buffs_Home extends Model_Buffs_Abstract_Buff {
	
	protected static $name = 'Insel der Ruhe';
	protected static $icon = 'home';
	protected static $desc = 'Endlich mal ein bisschen ausruhen. Zuhause ist dein Wasser- und Nahrungsverbrauch leicht reduziert.';
	protected static $bid = 'home';
	
	protected $effects = Array(		
				Model_Player::MP_STAT_HUNGER => Array(
					Model_Buffs_Abstract_Buff::MB_RAISE_ACC => 0,
					Model_Buffs_Abstract_Buff::MB_DROP_ACC => 0,
					Model_Buffs_Abstract_Buff::MB_RAISE_PRC => 0,
					Model_Buffs_Abstract_Buff::MB_DROP_PRC => -0.2,
				),				
				Model_Player::MP_STAT_THIRST => Array(
						Model_Buffs_Abstract_Buff::MB_RAISE_ACC => 0,
						Model_Buffs_Abstract_Buff::MB_DROP_ACC => 0,
						Model_Buffs_Abstract_Buff::MB_RAISE_PRC => 0,
						Model_Buffs_Abstract_Buff::MB_DROP_PRC => -0.3,
				),				
			);
}
