<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Buffs_Nuclear extends Model_Buffs_Abstract_Buff {
	
	protected static $name = 'Verstrahlung';
	protected static $icon = 'rad';
	protected static $bid = 'rad';
	protected static $visible = false;
	
	protected $effects = Array(
				Model_Player::MP_STAT_HEALTH => Array(
					Model_Buffs_Abstract_Buff::MB_RAISE_ACC => 0,
					Model_Buffs_Abstract_Buff::MB_DROP_ACC => 0,
					Model_Buffs_Abstract_Buff::MB_RAISE_PRC => 0,
					Model_Buffs_Abstract_Buff::MB_DROP_PRC => 0,
				),
				Model_Player::MP_STAT_ENERGY => Array(
					Model_Buffs_Abstract_Buff::MB_RAISE_ACC => 0,
					Model_Buffs_Abstract_Buff::MB_DROP_ACC => 0,
					Model_Buffs_Abstract_Buff::MB_RAISE_PRC => 0,
					Model_Buffs_Abstract_Buff::MB_DROP_PRC => 0,
				),
			);
	
	public function rebuild() {
		$rad  = $this->assoc_player->stats_get(Model_Player::MP_STAT_RADIATION)/100;

		$this->effects[Model_Player::MP_STAT_HEALTH] = Array(
				Model_Buffs_Abstract_Buff::MB_RAISE_ACC => 0,
				Model_Buffs_Abstract_Buff::MB_DROP_ACC => $rad,
				Model_Buffs_Abstract_Buff::MB_RAISE_PRC => ($rad > 0.5) ? (-2 * ($rad - 0.5)) : 0,
				Model_Buffs_Abstract_Buff::MB_DROP_PRC => 0,
		);
		
		$this->effects[Model_Player::MP_STAT_ENERGY] = Array(
				Model_Buffs_Abstract_Buff::MB_RAISE_ACC => 0,
				Model_Buffs_Abstract_Buff::MB_DROP_ACC => 0,
				Model_Buffs_Abstract_Buff::MB_RAISE_PRC => -$rad,
				Model_Buffs_Abstract_Buff::MB_DROP_PRC => $rad,
		);
		
		return parent::rebuild();
	}
}
