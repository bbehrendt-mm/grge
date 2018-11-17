<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Buffs_Nom extends Model_Buffs_Abstract_Buff {
	
	protected static $name = 'Ausgewogene Ernährung';
	protected static $icon = 'nom';
	protected static $desc = 'Du weist, wie man sich richtig ernährt! Da dein Körper mit allen wichtigen Nährstoffen versorgt ist, bekommst du nicht mehr so schnell Hunger. Zumindest für eine Weile...';
	protected static $bid = 'nom';

    protected function get_effects(): array { return [
        Model_Status::MS_STAT_HUNGER => Array(
            Model_Buffs_Abstract_Buff::MB_RAISE_ACC => 0,
            Model_Buffs_Abstract_Buff::MB_DROP_ACC => 0,
            Model_Buffs_Abstract_Buff::MB_RAISE_PRC => 0,
            Model_Buffs_Abstract_Buff::MB_DROP_PRC => -0.5,
        )
    ]; }
	
	public function merge($newclass) {
		$this->lifetime = $newclass->lifetime();
	}
}
