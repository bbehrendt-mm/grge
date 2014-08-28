<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Buffs_Blood extends Model_Buffs_Abstract_Buff {
	
	protected static $name = 'Schwere Blutung';
	protected static $icon = 'blood';
	protected static $desc = 'Du ziehst da eine ganz schöne Blutspur hinter dir her... Diese Wunde sieht nicht so aus als könnte sie von alleine heilen. Du solltest dir unbedingt eine Bandage besorgen...';
	protected static $bid = 'blood';
	
	protected $effects = Array(
				Model_Player::MP_STAT_ENERGY => Array(
					Model_Buffs_Abstract_Buff::MB_RAISE_ACC => 0,
					Model_Buffs_Abstract_Buff::MB_DROP_ACC => 0.3,
					Model_Buffs_Abstract_Buff::MB_RAISE_PRC => 0,
					Model_Buffs_Abstract_Buff::MB_DROP_PRC => 0,
				),
				Model_Player::MP_STAT_HEALTH => Array(
					Model_Buffs_Abstract_Buff::MB_RAISE_ACC => 0,
					Model_Buffs_Abstract_Buff::MB_DROP_ACC => 1.2,
					Model_Buffs_Abstract_Buff::MB_RAISE_PRC => 0,
					Model_Buffs_Abstract_Buff::MB_DROP_PRC => 0,
				),								
			);
}
