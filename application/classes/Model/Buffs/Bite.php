<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Buffs_Bite extends Model_Buffs_Abstract_Buff {
	
	protected static $name = 'Bisswunde';
	protected static $icon = 'bite';
	protected static $desc = 'Du hast im kampf eine Bisswunde davongetragen. Das ist soweit erstmal nichts schlimmes... solange du dir dadurch keine Infektion eingefangen hast.';
	protected static $bid = 'bite';

    public function __construct($association = NULL) {
        parent::__construct($association, 1);
    }

	public function merge($newclass) {
		$this->lifetime += $newclass->lifetime();
	}
	
	public function tick() {
        /** @global Model_Game $game */
		global $game;
        if ($game->config('game.bhav.infections') && !$this->assoc_player->get_status()->retrieve('immune') && $this->assoc_player->get_status()->get(Model_Status::MS_STAT_ZOMBIFY) <= 0 && (mt_rand(0,100) <= 5)) {
            $this->assoc_player->get_status()->set(Model_Status::MS_STAT_ZOMBIFY, 5);
            if ($this->associated_to_player()) $this->assoc_player->log()->add('Deine Bisswunde sieht aber gar nicht gut aus... offenbar hast du dich mit dem Zombievirus infiziert!');
        }
        return parent::tick();
    }
}
