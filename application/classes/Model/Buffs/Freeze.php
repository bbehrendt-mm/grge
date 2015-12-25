<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Buffs_Freeze extends Model_Buffs_Abstract_Buff {
	
	protected static $name = 'Eisige Kälte';
	protected static $icon = 'freeze';
	protected static $bid = 'freeze';
	protected static $visible = false;
	
	protected $effects = Array(
				Model_Status::MS_STAT_HEALTH => Array(
					Model_Buffs_Abstract_Buff::MB_RAISE_ACC => 0,
					Model_Buffs_Abstract_Buff::MB_DROP_ACC => 0,
					Model_Buffs_Abstract_Buff::MB_RAISE_PRC => 0,
					Model_Buffs_Abstract_Buff::MB_DROP_PRC => 0,
				),
				Model_Status::MS_STAT_ENERGY => Array(
					Model_Buffs_Abstract_Buff::MB_RAISE_ACC => 0,
					Model_Buffs_Abstract_Buff::MB_DROP_ACC => 0,
					Model_Buffs_Abstract_Buff::MB_RAISE_PRC => 0,
					Model_Buffs_Abstract_Buff::MB_DROP_PRC => 0,
				),
                Model_Status::MS_STAT_FREEZE => Array(
                    Model_Buffs_Abstract_Buff::MB_RAISE_ACC => 0,
                    Model_Buffs_Abstract_Buff::MB_DROP_ACC => 0,
                    Model_Buffs_Abstract_Buff::MB_RAISE_PRC => 0,
                    Model_Buffs_Abstract_Buff::MB_DROP_PRC => 0,
                ),
			);
	
	public function rebuild() {
		$cold = $this->assoc_player->get_status()->get(Model_Status::MS_STAT_FREEZE);

		if ($cold > 0) {
			$this->effects[Model_Status::MS_STAT_ENERGY] = Array(
					Model_Buffs_Abstract_Buff::MB_RAISE_ACC => 0,
					Model_Buffs_Abstract_Buff::MB_DROP_ACC => max(0,($cold-50)/50),
					Model_Buffs_Abstract_Buff::MB_RAISE_PRC => 0,
					Model_Buffs_Abstract_Buff::MB_DROP_PRC => $cold / 100,
			);
			
			$this->effects[Model_Status::MS_STAT_HEALTH] = Array(
					Model_Buffs_Abstract_Buff::MB_RAISE_ACC => 0,
					Model_Buffs_Abstract_Buff::MB_DROP_ACC => max(0,($cold-50)/40),
					Model_Buffs_Abstract_Buff::MB_RAISE_PRC => 0,
					Model_Buffs_Abstract_Buff::MB_DROP_PRC => 0,
			);
		} else {
			$this->effects[Model_Status::MS_STAT_ENERGY] = Array(
					Model_Buffs_Abstract_Buff::MB_RAISE_ACC => 0,
					Model_Buffs_Abstract_Buff::MB_DROP_ACC => 0,
					Model_Buffs_Abstract_Buff::MB_RAISE_PRC => 0,
					Model_Buffs_Abstract_Buff::MB_DROP_PRC => 0,
			);
				
			$this->effects[Model_Status::MS_STAT_HEALTH] = Array(
					Model_Buffs_Abstract_Buff::MB_RAISE_ACC => 0,
					Model_Buffs_Abstract_Buff::MB_DROP_ACC => 0,
					Model_Buffs_Abstract_Buff::MB_RAISE_PRC => 0,
					Model_Buffs_Abstract_Buff::MB_DROP_PRC => 0,
			);
		}
		
		return parent::rebuild();
	}
}
