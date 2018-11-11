<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Buffs_Couch extends Model_Buffs_Abstract_Fragile {
	
	protected static $name = 'Entspannen';
	protected static $desc = 'Du bist gerade dabei, dich ein wenig zu entspannen. Verdammt, warum hat dieses Versteck eigentlich keinen Kamin?';
	protected static $icon = 'couch';

    protected static $abortable = true;
	private $level = 0;
	
	public function __construct($player_id, $level) {
		$this->level = $level;
		parent::__construct($player_id, -1);
	}

    protected $effects = Array(
				Model_Status::MS_STAT_ENERGY => Array(
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
			);
	
	public function rebuild() {
        $c = 0;
        foreach (Tool_Scripts::at_location($this->assoc_player->location_class()) as $p)
            /** @var $p Model_Player */
            if ($p->get_status()->retrieve('fragile') && Tool_System::instance_of($p->get_status()->retrieve('fragile'), 'Model_Buffs_Couch'))
                $c++;

        if ($c > 5)
            $c = 5;

        if ($this->level == 1) {
            $this->effects[Model_Status::MS_STAT_ENERGY][Model_Buffs_Abstract_Buff::MB_RAISE_ACC] = 0.10 + $c * 0.05;
            $this->effects[Model_Status::MS_STAT_SLEEPY][Model_Buffs_Abstract_Buff::MB_DROP_ACC] = 0.25 - $c * 0.05;
        } elseif ($this->level == 2) {
            $this->effects[Model_Status::MS_STAT_ENERGY][Model_Buffs_Abstract_Buff::MB_RAISE_ACC] = 0.20 + $c * 0.1;
            $this->effects[Model_Status::MS_STAT_SLEEPY][Model_Buffs_Abstract_Buff::MB_DROP_ACC] = 0.35 - $c * 0.07;
        }


		return parent::rebuild();
	}

    protected function action_on_complete() {}
}
