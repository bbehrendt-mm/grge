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

        if ($this->assoc_player->get_status()->get(Model_Status::MS_STAT_SLEEPY) == 100
            || $this->assoc_player->get_status()->get(Model_Status::MS_STAT_HUNGER) < 20
            || $this->assoc_player->get_status()->get(Model_Status::MS_STAT_THIRST) < 20) {
            if ($this->associated_to_player()) $this->assoc_player->log()->add('Du kannst jetzt nicht schlafen!');
            return;
        }

		new Model_Buffs_Passout($this->assoc_player);
		new Model_Buffs_Sleeplock($this->assoc_player);
	}
	
	public function unbuff() {
		if ($buff = $this->assoc_player->get_status()->retrieve('passout')) $buff->unbuff();
        if ($buff = $this->assoc_player->get_status()->retrieve('fragile')) $buff->unbuff();

		return parent::unbuff();
	}

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
				Model_Status::MS_STAT_SLEEPY => Array(
                    Model_Buffs_Abstract_Buff::MB_RAISE_ACC => 0,
                    Model_Buffs_Abstract_Buff::MB_DROP_ACC => 0,
                    Model_Buffs_Abstract_Buff::MB_RAISE_PRC => 0,
                    Model_Buffs_Abstract_Buff::MB_DROP_PRC => 0,
				),
                Model_Status::MS_STAT_FREEZE => Array(
                    Model_Buffs_Abstract_Buff::MB_RAISE_ACC => 0,
                    Model_Buffs_Abstract_Buff::MB_DROP_ACC => 0,
                    Model_Buffs_Abstract_Buff::MB_RAISE_PRC => 0,
                    Model_Buffs_Abstract_Buff::MB_DROP_PRC => 0,
                ),
			);

    protected function get_effects(): array { return $this->effects; }
	
	public function rebuild() {
		if ($this->assoc_player->get_status()->get(Model_Status::MS_STAT_SLEEPY) == 100) {
			if ($this->associated_to_player()) $this->assoc_player->achievements()->achieve(Model_Achievement::MA_SLEEP);
			return $this->unbuff();
		}
		
		if ($this->assoc_player->get_status()->get(Model_Status::MS_STAT_HUNGER) < 20 ) {
            if ($this->associated_to_player()) $this->assoc_player->log()->add('Dein furchtbarer Hunger hindert dich am weiterschlafen... Du bist aufgewacht.');
			return $this->unbuff();
		}
		if ($this->assoc_player->get_status()->get(Model_Status::MS_STAT_THIRST) < 20 ) {
            if ($this->associated_to_player()) $this->assoc_player->log()->add('Dein furchtbarer Durst hindert dich am weiterschlafen... Du bist aufgewacht.');
			return $this->unbuff();
		}
		
		switch (abs($this->level)) {
			case 0: 
				$this->effects[Model_Status::MS_STAT_ENERGY][Model_Buffs_Abstract_Buff::MB_RAISE_ACC] = 0.25;
				$this->effects[Model_Status::MS_STAT_SLEEPY][Model_Buffs_Abstract_Buff::MB_RAISE_ACC] = 0.75;
				break;
			case 1:
				$this->effects[Model_Status::MS_STAT_ENERGY][Model_Buffs_Abstract_Buff::MB_RAISE_ACC] = $this->level > 0 ? 0.50 : 0.25;
				$this->effects[Model_Status::MS_STAT_SLEEPY][Model_Buffs_Abstract_Buff::MB_RAISE_ACC] = 1;
				$this->effects[Model_Status::MS_STAT_HEALTH][Model_Buffs_Abstract_Buff::MB_RAISE_PRC] = $this->level > 0 ? 0.5 : 0;
				break;
			case 2:
				$this->effects[Model_Status::MS_STAT_ENERGY][Model_Buffs_Abstract_Buff::MB_RAISE_ACC] = $this->level > 0 ? 0.75 : 0.5;
				$this->effects[Model_Status::MS_STAT_ENERGY][Model_Buffs_Abstract_Buff::MB_RAISE_PRC] = $this->level > 0 ? 0.5 : 0;
				$this->effects[Model_Status::MS_STAT_SLEEPY][Model_Buffs_Abstract_Buff::MB_RAISE_ACC] = 1;
				$this->effects[Model_Status::MS_STAT_HEALTH][Model_Buffs_Abstract_Buff::MB_RAISE_PRC] = $this->level > 0 ? 1 : 0;;
				break;
			case 3:
				$this->effects[Model_Status::MS_STAT_ENERGY][Model_Buffs_Abstract_Buff::MB_RAISE_ACC] = $this->level > 0 ? 1 : 0.75;
				$this->effects[Model_Status::MS_STAT_ENERGY][Model_Buffs_Abstract_Buff::MB_RAISE_PRC] = $this->level > 0 ? 0.75 : 0;
				$this->effects[Model_Status::MS_STAT_SLEEPY][Model_Buffs_Abstract_Buff::MB_RAISE_ACC] = 1;
				$this->effects[Model_Status::MS_STAT_HEALTH][Model_Buffs_Abstract_Buff::MB_RAISE_PRC] = $this->level > 0 ? 1.5 : 0;
				break;
		}

        if (Tool_Scripts::get_timeofday($this->assoc_player) == 'snowynight')
            $this->effects[Model_Status::MS_STAT_FREEZE][Model_Buffs_Abstract_Buff::MB_RAISE_PRC] = 1.5;

		return parent::rebuild();
	}
	

}
