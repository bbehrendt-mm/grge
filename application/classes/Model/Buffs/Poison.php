<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Buffs_Poison extends Model_Buffs_Abstract_Buff {
	
	protected static $name = 'Vergiftung';
	protected static $icon = 'poison';
	protected static $desc = 'Du fühlst dich nicht sonderlich gut... was damit zusammenhängen könnte, dass du vergiftet wurdest!';
	protected static $bid = 'poison';


    protected $effects = Array(
        Model_Status::MS_STAT_ENERGY => Array(
            Model_Buffs_Abstract_Buff::MB_RAISE_ACC => 0,
            Model_Buffs_Abstract_Buff::MB_DROP_ACC => 0.1,
            Model_Buffs_Abstract_Buff::MB_RAISE_PRC => 0,
            Model_Buffs_Abstract_Buff::MB_DROP_PRC => 0,
        ),
        Model_Status::MS_STAT_HEALTH => Array(
            Model_Buffs_Abstract_Buff::MB_RAISE_ACC => 0,
            Model_Buffs_Abstract_Buff::MB_DROP_ACC => 0.2,
            Model_Buffs_Abstract_Buff::MB_RAISE_PRC => -1,
            Model_Buffs_Abstract_Buff::MB_DROP_PRC => 0.5,
        ),
    );

    public function __construct($association = NULL) {
        parent::__construct($association, 2);
    }

	public function merge($newclass) {
		$this->lifetime += $newclass->lifetime();
        $this->lifetime = min(24, $this->lifetime);
	}
	
	public function tick() {
        /** @global Model_Game $game */
		global $game;
        if ($game->config('game.bhav.infections') && !$this->assoc_player->get_status()->retrieve('immune') && $this->assoc_player->get_status()->get(Model_Status::MS_STAT_ZOMBIFY) <= 0 && (mt_rand(0,100) <= 8)) {
            $this->assoc_player->get_status()->set(Model_Status::MS_STAT_ZOMBIFY, 5);
            if ($this->associated_to_player()) $this->assoc_player->log()->add('Deine Vergiftungssymptome sind schlimmer geworden... offenbar hast du dich mit dem Zombievirus infiziert!');
        }
        return parent::tick();
    }
}
