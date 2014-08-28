<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Buffs_Zombify extends Model_Buffs_Abstract_Buff {
	
	protected static $name = 'Zombifizierung';
	protected static $icon = 'zombify';
	protected static $bid = 'zombify';
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
                Model_Player::MP_STAT_HUNGER => Array(
                    Model_Buffs_Abstract_Buff::MB_RAISE_ACC => 0,
                    Model_Buffs_Abstract_Buff::MB_DROP_ACC => 0,
                    Model_Buffs_Abstract_Buff::MB_RAISE_PRC => 0,
                    Model_Buffs_Abstract_Buff::MB_DROP_PRC => 0,
                ),
                Model_Player::MP_STAT_THIRST => Array(
                    Model_Buffs_Abstract_Buff::MB_RAISE_ACC => 0,
                    Model_Buffs_Abstract_Buff::MB_DROP_ACC => 0,
                    Model_Buffs_Abstract_Buff::MB_RAISE_PRC => 0,
                    Model_Buffs_Abstract_Buff::MB_DROP_PRC => 0,
                ),
                Model_Player::MP_STAT_SLEEPY => Array(
                    Model_Buffs_Abstract_Buff::MB_RAISE_ACC => 0,
                    Model_Buffs_Abstract_Buff::MB_DROP_ACC => 0,
                    Model_Buffs_Abstract_Buff::MB_RAISE_PRC => 0,
                    Model_Buffs_Abstract_Buff::MB_DROP_PRC => 0,
                ),
			);
	
	public function rebuild() {
		$zombify = $this->assoc_player->stats_get(Model_Player::MP_STAT_ZOMBIFY);
        $health = $this->assoc_player->stats_get(Model_Player::MP_STAT_HEALTH);
		
		if ($zombify > 0) {
			$this->effects[Model_Player::MP_STAT_HEALTH] = Array(
                Model_Buffs_Abstract_Buff::MB_RAISE_ACC => 0,
                Model_Buffs_Abstract_Buff::MB_DROP_ACC => ($health > (100-$zombify)) ? 1 : 0,
                Model_Buffs_Abstract_Buff::MB_RAISE_PRC => -$zombify/100,
                Model_Buffs_Abstract_Buff::MB_DROP_PRC => 0,
			);
			$this->effects[Model_Player::MP_STAT_ENERGY] = Array(
                Model_Buffs_Abstract_Buff::MB_RAISE_ACC => 0,
                Model_Buffs_Abstract_Buff::MB_DROP_ACC => 0,
                Model_Buffs_Abstract_Buff::MB_RAISE_PRC => -$zombify/100,
                Model_Buffs_Abstract_Buff::MB_DROP_PRC => -$zombify/100,
			);
            $this->effects[Model_Player::MP_STAT_HUNGER] = Array(
                Model_Buffs_Abstract_Buff::MB_RAISE_ACC => 0,
                Model_Buffs_Abstract_Buff::MB_DROP_ACC => 0,
                Model_Buffs_Abstract_Buff::MB_RAISE_PRC => 0,
                Model_Buffs_Abstract_Buff::MB_DROP_PRC => $zombify/100,
            );
            $this->effects[Model_Player::MP_STAT_THIRST] = Array(
                Model_Buffs_Abstract_Buff::MB_RAISE_ACC => 0,
                Model_Buffs_Abstract_Buff::MB_DROP_ACC => 0,
                Model_Buffs_Abstract_Buff::MB_RAISE_PRC => 0,
                Model_Buffs_Abstract_Buff::MB_DROP_PRC => -$zombify/100,
            );
            $this->effects[Model_Player::MP_STAT_SLEEPY] = Array(
                Model_Buffs_Abstract_Buff::MB_RAISE_ACC => 0,
                Model_Buffs_Abstract_Buff::MB_DROP_ACC => 0,
                Model_Buffs_Abstract_Buff::MB_RAISE_PRC => 0,
                Model_Buffs_Abstract_Buff::MB_DROP_PRC => -$zombify/100,
            );
            $this->effects[Model_Player::MP_STAT_ZOMBIFY] = Array(
                Model_Buffs_Abstract_Buff::MB_RAISE_ACC => $zombify/500,
                Model_Buffs_Abstract_Buff::MB_DROP_ACC => 0,
                Model_Buffs_Abstract_Buff::MB_RAISE_PRC => 0,
                Model_Buffs_Abstract_Buff::MB_DROP_PRC => 0,
            );
		} else {
            $this->effects[Model_Player::MP_STAT_HEALTH] = Array(
                Model_Buffs_Abstract_Buff::MB_RAISE_ACC => 0,
                Model_Buffs_Abstract_Buff::MB_DROP_ACC => 0,
                Model_Buffs_Abstract_Buff::MB_RAISE_PRC => 0,
                Model_Buffs_Abstract_Buff::MB_DROP_PRC => 0,
            );
            $this->effects[Model_Player::MP_STAT_ENERGY] = Array(
                Model_Buffs_Abstract_Buff::MB_RAISE_ACC => 0,
                Model_Buffs_Abstract_Buff::MB_DROP_ACC => 0,
                Model_Buffs_Abstract_Buff::MB_RAISE_PRC => 0,
                Model_Buffs_Abstract_Buff::MB_DROP_PRC => 0,
            );
            $this->effects[Model_Player::MP_STAT_HUNGER] = Array(
                Model_Buffs_Abstract_Buff::MB_RAISE_ACC => 0,
                Model_Buffs_Abstract_Buff::MB_DROP_ACC => 0,
                Model_Buffs_Abstract_Buff::MB_RAISE_PRC => 0,
                Model_Buffs_Abstract_Buff::MB_DROP_PRC => 0,
            );
            $this->effects[Model_Player::MP_STAT_THIRST] = Array(
                Model_Buffs_Abstract_Buff::MB_RAISE_ACC => 0,
                Model_Buffs_Abstract_Buff::MB_DROP_ACC => 0,
                Model_Buffs_Abstract_Buff::MB_RAISE_PRC => 0,
                Model_Buffs_Abstract_Buff::MB_DROP_PRC => 0,
            );
            $this->effects[Model_Player::MP_STAT_SLEEPY] = Array(
                Model_Buffs_Abstract_Buff::MB_RAISE_ACC => 0,
                Model_Buffs_Abstract_Buff::MB_DROP_ACC => 0,
                Model_Buffs_Abstract_Buff::MB_RAISE_PRC => 0,
                Model_Buffs_Abstract_Buff::MB_DROP_PRC => 0,
            );
            $this->effects[Model_Player::MP_STAT_ZOMBIFY] = Array(
                Model_Buffs_Abstract_Buff::MB_RAISE_ACC => 0,
                Model_Buffs_Abstract_Buff::MB_DROP_ACC => 0,
                Model_Buffs_Abstract_Buff::MB_RAISE_PRC => 0,
                Model_Buffs_Abstract_Buff::MB_DROP_PRC => 0,
            );
		}
		
		return parent::rebuild();
	}
}
