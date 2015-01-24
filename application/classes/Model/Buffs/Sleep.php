<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Buffs_Sleep extends Model_Buffs_Abstract_Buff {
	
	protected static $name = 'Schlafen';
	protected static $desc = 'Du schläfst momentan, deine Aktionsmöglichkeiten sind auf Herumwinden beschränkt. Dafür regenerierst du wenigstens etwas Energie.';
	protected static $bid = 'sleep_cozy';
	protected static $icon = 'sleep_cozy';
	
	private $level = 0;
	
	public function __construct($player_id, $level) {
		$this->level = $level;
		parent::__construct($player_id, -1);

        if ($this->assoc_player->stats_get(Model_Player::MP_STAT_SLEEPY) == 100
            || $this->assoc_player->stats_get(Model_Player::MP_STAT_HUNGER) < 20
            || $this->assoc_player->stats_get(Model_Player::MP_STAT_THIRST) < 20) {
            $this->assoc_player->log()->add('Du kannst jetzt nicht schlafen!');
            return;
        }


		new Model_Buffs_Passout($this->assoc_player->id());
		new Model_Buffs_Sleeplock($this->assoc_player->id());
	}
	
	public function unbuff() {
		if ($buff = $this->assoc_player->buff_retr('passout')) $buff->unbuff();
        if ($buff = $this->assoc_player->buff_retr('fragile')) $buff->unbuff();

		return parent::unbuff();
	}
	
	protected $effects = Array(
				Model_Player::MP_STAT_ENERGY => Array(
					Model_Buffs_Abstract_Buff::MB_RAISE_ACC => 0,
					Model_Buffs_Abstract_Buff::MB_DROP_ACC => 0,
					Model_Buffs_Abstract_Buff::MB_RAISE_PRC => 0,
					Model_Buffs_Abstract_Buff::MB_DROP_PRC => 0,
				),
				Model_Player::MP_STAT_HEALTH => Array(
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
		if ($this->assoc_player->stats_get(Model_Player::MP_STAT_SLEEPY) == 100) {
			$this->assoc_player->achievements()->achieve(Model_Achievement::MA_SLEEP);
			return $this->unbuff();
		}
		
		if ($this->assoc_player->stats_get(Model_Player::MP_STAT_HUNGER) < 20 ) {
			$this->assoc_player->log()->add('Dein furchtbarer Hunger hindert dich am weiterschlafen... Du bist aufgewacht.');
			return $this->unbuff();
		}
		if ($this->assoc_player->stats_get(Model_Player::MP_STAT_THIRST) < 20 ) {
			$this->assoc_player->log()->add('Dein furchtbarer Durst hindert dich am weiterschlafen... Du bist aufgewacht.');
			return $this->unbuff();
		}
		
		switch ($this->level) {
			case 0: 
				$this->effects[Model_Player::MP_STAT_ENERGY][Model_Buffs_Abstract_Buff::MB_RAISE_ACC] = 0.25;
				$this->effects[Model_Player::MP_STAT_SLEEPY][Model_Buffs_Abstract_Buff::MB_RAISE_ACC] = 0.75;
				break;
			case 1:
				$this->effects[Model_Player::MP_STAT_ENERGY][Model_Buffs_Abstract_Buff::MB_RAISE_ACC] = 0.50;
				$this->effects[Model_Player::MP_STAT_SLEEPY][Model_Buffs_Abstract_Buff::MB_RAISE_ACC] = 1;
				$this->effects[Model_Player::MP_STAT_HEALTH][Model_Buffs_Abstract_Buff::MB_RAISE_PRC] = 0.5;
				break;
			case 2:
				$this->effects[Model_Player::MP_STAT_ENERGY][Model_Buffs_Abstract_Buff::MB_RAISE_ACC] = 0.75;
				$this->effects[Model_Player::MP_STAT_ENERGY][Model_Buffs_Abstract_Buff::MB_RAISE_PRC] = 0.5;
				$this->effects[Model_Player::MP_STAT_SLEEPY][Model_Buffs_Abstract_Buff::MB_RAISE_ACC] = 1;
				$this->effects[Model_Player::MP_STAT_HEALTH][Model_Buffs_Abstract_Buff::MB_RAISE_PRC] = 1;
				break;
			case 3:
				$this->effects[Model_Player::MP_STAT_ENERGY][Model_Buffs_Abstract_Buff::MB_RAISE_ACC] = 1;
				$this->effects[Model_Player::MP_STAT_ENERGY][Model_Buffs_Abstract_Buff::MB_RAISE_PRC] = 0.75;
				$this->effects[Model_Player::MP_STAT_SLEEPY][Model_Buffs_Abstract_Buff::MB_RAISE_ACC] = 1;
				$this->effects[Model_Player::MP_STAT_HEALTH][Model_Buffs_Abstract_Buff::MB_RAISE_PRC] = 1.5;
				break;
		}
		
		return parent::rebuild();
	}
	

}
