<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Buffs_Job_Survivalist extends Model_Buffs_Abstract_Job {
	
	protected static $namelist = Array('Survivalist');
	protected static $desclist = Array('Dank deiner Erfahrung findest du schneller Gegenstände.');
	
	protected static $bid = 'survivalist';
	
	protected $effects = Array(
			Model_Status::MS_CHAR_ITEM_SPAWNRATE => Array(
					Model_Buffs_Abstract_Buff::MB_RAISE_ACC => 0,
					Model_Buffs_Abstract_Buff::MB_DROP_ACC => 0,
					Model_Buffs_Abstract_Buff::MB_RAISE_PRC => 0,
					Model_Buffs_Abstract_Buff::MB_DROP_PRC => 0,
			),
	);

    protected function get_effects(): array { return $this->effects; }
	
	protected function adjust(): void
    {
		$this->effects[Model_Status::MS_CHAR_ITEM_SPAWNRATE][Model_Buffs_Abstract_Buff::MB_RAISE_ACC] = $this->level >= 5 ? 0.15 : 0.05;
	}	
}
