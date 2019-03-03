<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Buffs_Alcohol extends Model_Buffs_Abstract_Buff {
	
	protected static $name = 'Alkohol';
	protected static $icon = 'alcohol';
	protected static $bid = 'alcohol';
	protected static $visible = false;

    protected $effects = Array(
				Model_Status::MS_STAT_HEALTH => Array(
					Model_Buffs_Abstract_Buff::MB_RAISE_ACC => 0,
					Model_Buffs_Abstract_Buff::MB_DROP_ACC => 0,
					Model_Buffs_Abstract_Buff::MB_RAISE_PRC => 0,
					Model_Buffs_Abstract_Buff::MB_DROP_PRC => 0,
				),
				Model_Status::MS_CHAR_EVASIVENESS => Array(
					Model_Buffs_Abstract_Buff::MB_RAISE_ACC => 0,
					Model_Buffs_Abstract_Buff::MB_DROP_ACC => 0,
					Model_Buffs_Abstract_Buff::MB_RAISE_PRC => 0,
					Model_Buffs_Abstract_Buff::MB_DROP_PRC => 0,
				),
                Model_Status::MS_CHAR_ITEM_SPAWNRATE => Array(
                    Model_Buffs_Abstract_Buff::MB_RAISE_ACC => 0,
                    Model_Buffs_Abstract_Buff::MB_DROP_ACC => 0,
                    Model_Buffs_Abstract_Buff::MB_RAISE_PRC => 0,
                    Model_Buffs_Abstract_Buff::MB_DROP_PRC => 0,
                ),
	);
    protected function get_effects(): array { return $this->effects; }
	
	public function rebuild(): bool
    {
		$drunk = $this->assoc_player->get_status()->get(Model_Status::MS_STAT_DRUNK);
		
		if ($drunk > 0) {
			$this->effects[Model_Status::MS_CHAR_EVASIVENESS] = Array(
					Model_Buffs_Abstract_Buff::MB_RAISE_ACC => 0,
					Model_Buffs_Abstract_Buff::MB_DROP_ACC => $drunk / 100,
					Model_Buffs_Abstract_Buff::MB_RAISE_PRC => 0,
					Model_Buffs_Abstract_Buff::MB_DROP_PRC => 0,
			);
			
			$this->effects[Model_Status::MS_STAT_HEALTH] = Array(
					Model_Buffs_Abstract_Buff::MB_RAISE_ACC => 0,
					Model_Buffs_Abstract_Buff::MB_DROP_ACC => max(0,($drunk-50)/300),
					Model_Buffs_Abstract_Buff::MB_RAISE_PRC => 0,
					Model_Buffs_Abstract_Buff::MB_DROP_PRC => 0,
			);

            $this->effects[Model_Status::MS_CHAR_ITEM_SPAWNRATE] = Array(
                Model_Buffs_Abstract_Buff::MB_RAISE_ACC => 0,
                Model_Buffs_Abstract_Buff::MB_DROP_ACC => min(1, max(0,$drunk/100)),
                Model_Buffs_Abstract_Buff::MB_RAISE_PRC => 0,
                Model_Buffs_Abstract_Buff::MB_DROP_PRC => 0,
            );
		} else {
			$this->effects[Model_Status::MS_CHAR_EVASIVENESS] = Array(
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

            $this->effects[Model_Status::MS_CHAR_ITEM_SPAWNRATE] = Array(
                Model_Buffs_Abstract_Buff::MB_RAISE_ACC => 0,
                Model_Buffs_Abstract_Buff::MB_DROP_ACC => 0,
                Model_Buffs_Abstract_Buff::MB_RAISE_PRC => 0,
                Model_Buffs_Abstract_Buff::MB_DROP_PRC => 0,
            );
		}
		
		return parent::rebuild();
	}
}
