<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Buffs_Job_Coach extends Model_Buffs_Abstract_Job {
	
	protected static $name = Array('Footballer-Statur');
	protected static $desc = Array(	'Dank deiner beeindruckenden Statur kannst du mehr Schaden einstecken und bei der Flucht ein paar Extra-Zombies aus dem Weg räumen.');
	
	protected static $bid = 'coach';
	
	protected $effects = Array(
			Model_Status::MS_CHAR_DAMAGE_RESISTANCE => Array(
					Model_Buffs_Abstract_Buff::MB_RAISE_ACC => 0,
					Model_Buffs_Abstract_Buff::MB_DROP_ACC => 0,
					Model_Buffs_Abstract_Buff::MB_RAISE_PRC => 0,
					Model_Buffs_Abstract_Buff::MB_DROP_PRC => 0,
			),
			Model_Status::MS_CHAR_BULKYNESS => Array(
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
	);

    protected function get_effects(): array { return $this->effects; }
	
	protected function adjust() {
		$this->effects[Model_Status::MS_CHAR_DAMAGE_RESISTANCE][Model_Buffs_Abstract_Buff::MB_DROP_ACC] = $this->level * 0.025;
		$this->effects[Model_Status::MS_CHAR_BULKYNESS][Model_Buffs_Abstract_Buff::MB_RAISE_ACC] = $this->level * 0.03;
		$this->effects[Model_Status::MS_CHAR_EVASIVENESS][Model_Buffs_Abstract_Buff::MB_RAISE_ACC] = $this->level * 0.015;
	}	
}
