<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Buffs_Scarecrow extends Model_Buffs_Abstract_Buff {
	
	protected static $name = 'Grausame Vogelscheuche';
	protected static $icon = 'scarecrow';
	protected static $desc = 'Wenn dieses furchtbare Ding dich die ganze Zeit anglotzt ist es kaum möglich, zu entspannen. Bis Halloween vorbei ist wirst du auf jeden Fall keinen geruhsamen Schlaf mehr haben...';
	protected static $bid = 'scarecrow';
	
	protected $effects = Array(
			Model_Player::MP_STAT_SLEEPY => Array(
					Model_Buffs_Abstract_Buff::MB_RAISE_ACC => 0,
					Model_Buffs_Abstract_Buff::MB_DROP_ACC => 0,
					Model_Buffs_Abstract_Buff::MB_RAISE_PRC => -0.1,
					Model_Buffs_Abstract_Buff::MB_DROP_PRC => 0,
			),
	);
}
