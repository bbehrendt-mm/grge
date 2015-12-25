<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Buffs_Drug3 extends Model_Buffs_Abstract_Buff {
	
	protected static $name = 'Entzugserscheinungen';
	protected static $icon = 'drug3';
	protected static $desc = 'Wie lange ist dein letzter Schuss her? Du weist es nicht mehr... es scheint eine Ewigkeit zu sein. Wirst du den Entzug durchhalten oder wieder zur Nadel greifen?';
	protected static $bid = 'drug3';
	
	protected $effects = Array(
				Model_Status::MS_STAT_ENERGY => Array(
					Model_Buffs_Abstract_Buff::MB_RAISE_ACC => 0,
					Model_Buffs_Abstract_Buff::MB_DROP_ACC => 0,
					Model_Buffs_Abstract_Buff::MB_RAISE_PRC => -0.6,
					Model_Buffs_Abstract_Buff::MB_DROP_PRC => 0.1,
				),
				Model_Status::MS_STAT_HEALTH => Array(
					Model_Buffs_Abstract_Buff::MB_RAISE_ACC => 0,
					Model_Buffs_Abstract_Buff::MB_DROP_ACC => 0,
					Model_Buffs_Abstract_Buff::MB_RAISE_PRC => -2,
					Model_Buffs_Abstract_Buff::MB_DROP_PRC => 0,
				),				
				Model_Status::MS_STAT_HUNGER => Array(
					Model_Buffs_Abstract_Buff::MB_RAISE_ACC => 0,
					Model_Buffs_Abstract_Buff::MB_DROP_ACC => 0,
					Model_Buffs_Abstract_Buff::MB_RAISE_PRC => 0,
					Model_Buffs_Abstract_Buff::MB_DROP_PRC => -0.3,
				),				
				Model_Status::MS_STAT_THIRST => Array(
						Model_Buffs_Abstract_Buff::MB_RAISE_ACC => 0,
						Model_Buffs_Abstract_Buff::MB_DROP_ACC => 0,
						Model_Buffs_Abstract_Buff::MB_RAISE_PRC => 0,
						Model_Buffs_Abstract_Buff::MB_DROP_PRC => 1,
				),				
			);
	
	public function unbuff() {
		if ($this->lifetime <= 0)
			if ($buff = $this->assoc_player->get_status()->retrieve('drug2')) {
				$buff->unbuff();
				if ($this->associated_to_player()) $this->assoc_player->log()->add(new Model_Log_Types_Text('Drogensucht', null, 'Du hast unglaubliche Willenskraft bewiesen und den kalten Entzug überstanden! Herzlichen Glückwunsch, deine Drogensucht ist Geschichte!'));
			}
		parent::unbuff();
	}
}
