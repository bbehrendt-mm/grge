<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Buffs_Metabolism extends Model_Buffs_Abstract_Buff {
	
	protected static $name = 'Metabolismus';
	protected static $icon = 'metabolism';
	protected static $desc = 'In deinem Körper laufen jederzeit unglaublich viele biochemische Prozesse ab, die zwar kein Mensch versteht, aber die dich irgendwie am Laufen halten. ';
	protected static $bid = 'metabolism';
    protected static $remotable = false;
    protected static $enabled = true;
	
	protected $effects = Array(
				Model_Status::MS_STAT_ENERGY => Array(
					Model_Buffs_Abstract_Buff::MB_RAISE_ACC => 0,
					Model_Buffs_Abstract_Buff::MB_DROP_ACC => 0,
					Model_Buffs_Abstract_Buff::MB_RAISE_PRC => 0,
					Model_Buffs_Abstract_Buff::MB_DROP_PRC => 0,
				),
				Model_Status::MS_STAT_HEALTH => Array(
					Model_Buffs_Abstract_Buff::MB_RAISE_ACC => 0,
					Model_Buffs_Abstract_Buff::MB_DROP_ACC => 0,
					Model_Buffs_Abstract_Buff::MB_RAISE_PRC => 0,
					Model_Buffs_Abstract_Buff::MB_DROP_PRC => 0,
				),				
				Model_Status::MS_STAT_HUNGER => Array(
					Model_Buffs_Abstract_Buff::MB_RAISE_ACC => 0,
					Model_Buffs_Abstract_Buff::MB_DROP_ACC => 0,
					Model_Buffs_Abstract_Buff::MB_RAISE_PRC => 0,
					Model_Buffs_Abstract_Buff::MB_DROP_PRC => 0,
				),				
				Model_Status::MS_STAT_THIRST => Array(
					Model_Buffs_Abstract_Buff::MB_RAISE_ACC => 0,
					Model_Buffs_Abstract_Buff::MB_DROP_ACC => 0,
					Model_Buffs_Abstract_Buff::MB_RAISE_PRC => 0,
					Model_Buffs_Abstract_Buff::MB_DROP_PRC => 0,
				),				
				Model_Status::MS_STAT_SLEEPY => Array(
					Model_Buffs_Abstract_Buff::MB_RAISE_ACC => 0,
					Model_Buffs_Abstract_Buff::MB_DROP_ACC => 0,
					Model_Buffs_Abstract_Buff::MB_RAISE_PRC => 0,
					Model_Buffs_Abstract_Buff::MB_DROP_PRC => 0,
				),				
				Model_Status::MS_STAT_DRUNK => Array(
					Model_Buffs_Abstract_Buff::MB_RAISE_ACC => 0,
					Model_Buffs_Abstract_Buff::MB_DROP_ACC => 0,
					Model_Buffs_Abstract_Buff::MB_RAISE_PRC => 0,
					Model_Buffs_Abstract_Buff::MB_DROP_PRC => 0,
				),
				Model_Status::MS_STAT_RADIATION => Array(
					Model_Buffs_Abstract_Buff::MB_RAISE_ACC => 0,
					Model_Buffs_Abstract_Buff::MB_DROP_ACC => 0,
					Model_Buffs_Abstract_Buff::MB_RAISE_PRC => 0,
					Model_Buffs_Abstract_Buff::MB_DROP_PRC => 0,
				),
			);
	
	public function rebuild() {
		if (static::$enabled) {
            $this->statchange_misc();
            $this->statchange_energy();
            $this->statchange_health();
            $this->statchange_hunger();
            $this->statchange_sleepy();
            $this->statchange_thirst();
        }

		return parent::rebuild();
	}
	
	private function statchange_hunger() {
		$this->effects[Model_Status::MS_STAT_HUNGER] = Array(
					Model_Buffs_Abstract_Buff::MB_RAISE_ACC => 0,
					Model_Buffs_Abstract_Buff::MB_DROP_ACC => 0.15,
					Model_Buffs_Abstract_Buff::MB_RAISE_PRC => 0,
					Model_Buffs_Abstract_Buff::MB_DROP_PRC => 0,
				);
	}
	
	private function statchange_thirst() {
		$this->effects[Model_Status::MS_STAT_THIRST] = Array(
				Model_Buffs_Abstract_Buff::MB_RAISE_ACC => 0,
				Model_Buffs_Abstract_Buff::MB_DROP_ACC => 0.25,
				Model_Buffs_Abstract_Buff::MB_RAISE_PRC => 0,
				Model_Buffs_Abstract_Buff::MB_DROP_PRC => 0,
		);
	}
	
	private function statchange_misc() {
		$this->effects[Model_Status::MS_STAT_DRUNK] = Array(
				Model_Buffs_Abstract_Buff::MB_RAISE_ACC => 0,
				Model_Buffs_Abstract_Buff::MB_DROP_ACC => 0.83,
				Model_Buffs_Abstract_Buff::MB_RAISE_PRC => 0,
				Model_Buffs_Abstract_Buff::MB_DROP_PRC => 0,
		);
		$this->effects[Model_Status::MS_STAT_RADIATION] = Array(
				Model_Buffs_Abstract_Buff::MB_RAISE_ACC => 0,
				Model_Buffs_Abstract_Buff::MB_DROP_ACC => 0.25,
				Model_Buffs_Abstract_Buff::MB_RAISE_PRC => 0,
				Model_Buffs_Abstract_Buff::MB_DROP_PRC => 0,
		);
	}
	
	private function statchange_sleepy() {
		$energy = $this->assoc_player->get_status()->get(Model_Status::MS_STAT_ENERGY);
		
		$this->effects[Model_Status::MS_STAT_SLEEPY] = Array(
				Model_Buffs_Abstract_Buff::MB_RAISE_ACC => 0,
				Model_Buffs_Abstract_Buff::MB_DROP_ACC => 0.25 + (100-$energy)/200,
				Model_Buffs_Abstract_Buff::MB_RAISE_PRC => 0,
				Model_Buffs_Abstract_Buff::MB_DROP_PRC => 0,
		);
	}
	
	private function statchange_health() {
		$energy = $this->assoc_player->get_status()->get(Model_Status::MS_STAT_ENERGY);
		$health = $this->assoc_player->get_status()->get(Model_Status::MS_STAT_HEALTH);
		$hunger = $this->assoc_player->get_status()->get(Model_Status::MS_STAT_HUNGER);
		$thirst = $this->assoc_player->get_status()->get(Model_Status::MS_STAT_THIRST);
		$sleepy = $this->assoc_player->get_status()->get(Model_Status::MS_STAT_SLEEPY);
		
		$ndif = 1;
		if		($thirst > 90)	$ndif += 0.003;
		elseif	($thirst > 75)	$ndif += 0.002;
		elseif	($thirst > 60)	$ndif += 0.001;
		elseif	($thirst > 40)	$ndif += 0;
		elseif	($thirst > 30)	$ndif -= 0.001;
		elseif	($thirst > 20)	$ndif -= 0.005;
		elseif	($thirst > 10)	$ndif -= 0.010;
		else					$ndif -= 0.050;
	
		if		($hunger > 90)	$ndif += 0.002;
		elseif	($hunger > 75)	$ndif += 0.0015;
		elseif	($hunger > 60)	$ndif += 0.0008;
		elseif	($hunger > 40)	$ndif += 0;
		elseif	($hunger > 30)	$ndif -= 0.001;
		elseif	($hunger > 20)	$ndif -= 0.004;
		elseif	($hunger > 10)	$ndif -= 0.008;
		else					$ndif -= 0.040;

		$this->effects[Model_Status::MS_STAT_HEALTH] = Array(
				Model_Buffs_Abstract_Buff::MB_RAISE_ACC => ($ndif >= 1) ? ($health * $ndif) - $health : 0,
				Model_Buffs_Abstract_Buff::MB_DROP_ACC => ($ndif >= 1) ? 0 : -((200 - $health) - ((200 - $health) * (2-$ndif))),
				Model_Buffs_Abstract_Buff::MB_RAISE_PRC => 0,
				Model_Buffs_Abstract_Buff::MB_DROP_PRC => 0,
		);	
	}
	
	private function statchange_energy() {
		$hunger = $this->assoc_player->get_status()->get(Model_Status::MS_STAT_HUNGER);
		$thirst = $this->assoc_player->get_status()->get(Model_Status::MS_STAT_THIRST);
		
		$ndif = 0;
		if		($thirst > 90)	$ndif += 0.08;
		elseif	($thirst > 75)	$ndif += 0.05;
		elseif	($thirst > 60)	$ndif += 0.02;
	
		if		($hunger > 90)	$ndif += 0.08;
		elseif	($hunger > 75)	$ndif += 0.05;
		elseif	($hunger > 60)	$ndif += 0.02;
	
		$ndif = $ndif * ($thirst/100);
	
		if		($thirst < 10)	$ndif -= 0.04;
		elseif	($thirst < 20)	$ndif -= 0.02;
		elseif	($thirst < 30)	$ndif -= 0.01;
	
		if		($hunger < 10)	$ndif -= 0.04;
		elseif	($hunger < 20)	$ndif -= 0.02;
		elseif	($hunger < 30)	$ndif -= 0.01;
	
		$this->effects[Model_Status::MS_STAT_ENERGY] = Array(
				Model_Buffs_Abstract_Buff::MB_RAISE_ACC => ($ndif < 0) ? 0 : $ndif,
				Model_Buffs_Abstract_Buff::MB_DROP_ACC => ($ndif > 0) ? 0 : $ndif,
				Model_Buffs_Abstract_Buff::MB_RAISE_PRC => 0,
				Model_Buffs_Abstract_Buff::MB_DROP_PRC => 0,
		);
	}
}
