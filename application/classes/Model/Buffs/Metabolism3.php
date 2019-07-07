<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Buffs_Metabolism3 extends Model_Buffs_Metabolism {
	
	public function rebuild(): bool
    {
        if (parent::rebuild()) {
            $this->statchange_child2();
            return true;
        }
        return false;
	}
	
	private function statchange_child2(): void {
        $this->effects[Model_Status::MS_STAT_DRUNK][Model_Buffs_Abstract_Buff::MB_DROP_ACC] = 0.2;
	}
}
