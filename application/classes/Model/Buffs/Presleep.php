<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Buffs_Presleep extends Model_Buffs_Abstract_Fragile {
	
	protected static $name = 'Einschlafen';
	protected static $desc = 'Du versuchst momentan, einzuschlafen. Bei all den furchtbaren Zombies da draußen ist das gar nicht so einfach - plane hierfür also lieber etwas mehr Zeit ein.';
	protected static $icon = 'sleep_pre';

    protected static $abortable = true;
	private $level = 0;

    protected function action_on_complete() {
        new Model_Buffs_Sleep($this->assoc_player->id(), $this->level);
    }
	
	public function __construct($player_id, $level, $duration = 5) {
		$this->level = $level;
		parent::__construct($player_id, $duration);
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
			$this->assoc_player->log()->add(new Model_Log_Types_Text(null, null, 'Dein furchtbarer Hunger hindert dich am weiterschlafen... Du bist aufgewacht.'));
			return $this->unbuff();
		}
		if ($this->assoc_player->stats_get(Model_Player::MP_STAT_THIRST) < 20 ) {
			$this->assoc_player->log()->add(new Model_Log_Types_Text(null, null, 'Dein furchtbarer Durst hindert dich am weiterschlafen... Du bist aufgewacht.'));
			return $this->unbuff();
		}

        $this->effects[Model_Player::MP_STAT_ENERGY][Model_Buffs_Abstract_Buff::MB_RAISE_ACC] = 0.15;
        $this->effects[Model_Player::MP_STAT_SLEEPY][Model_Buffs_Abstract_Buff::MB_RAISE_ACC] = 0.15;

		return parent::rebuild();
	}
	

}
