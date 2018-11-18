<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Buffs_Metabolism2 extends Model_Buffs_Metabolism {
	
	protected static $name = 'Kindlicher Metabolismus';
	protected static $icon = 'metabolism2';
	protected static $desc = 'In deinem Körper laufen jederzeit unglaublich viele biochemische Prozesse ab, die zwar kein Mensch versteht, aber die dich irgendwie am Laufen halten. ';
	protected static $bid = 'metabolism';
    protected static $dominance = Model_Buffs_Abstract_Buff::MBR_PARTIALLY_DOMINANT;
	
	public function rebuild(): bool
    {
        if (parent::rebuild()) {
            $this->statchange_child();
            return true;
        }
        return false;
	}
	
	private function statchange_child(): void
    {
		$this->effects[Model_Status::MS_STAT_HUNGER][Model_Buffs_Abstract_Buff::MB_DROP_PRC] = -0.1;
        $this->effects[Model_Status::MS_STAT_THIRST][Model_Buffs_Abstract_Buff::MB_DROP_PRC] = -0.1;
        $this->effects[Model_Status::MS_STAT_DRUNK][Model_Buffs_Abstract_Buff::MB_DROP_ACC] = 0.71;
        $this->effects[Model_Status::MS_STAT_RADIATION][Model_Buffs_Abstract_Buff::MB_DROP_ACC] = 0.35;
        $this->effects[Model_Status::MS_STAT_SLEEPY][Model_Buffs_Abstract_Buff::MB_DROP_PRC] = 0.15;
        $this->effects[Model_Status::MS_STAT_HEALTH][Model_Buffs_Abstract_Buff::MB_RAISE_PRC] = 1.5;
        $this->effects[Model_Status::MS_STAT_ENERGY][Model_Buffs_Abstract_Buff::MB_RAISE_PRC] = 1.4;
	}
}
