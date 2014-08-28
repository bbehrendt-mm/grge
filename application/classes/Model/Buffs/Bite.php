<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Buffs_Bite extends Model_Buffs_Abstract_Buff {
	
	protected static $name = 'Bisswunde';
	protected static $icon = 'bite';
	protected static $desc = 'Du hast im kampf eine Bisswunde davongetragen. Das ist soweit erstmal nichts schlimmes... solange du dir dadurch keine Infektion eingefangen hast.';
	protected static $bid = 'bite';
	
	public function merge($newclass) {
		$this->lifetime += $newclass->lifetime();
	}
	
	public function tick() {
        global $game;
        if ($game->config('game.bhav.infections') && !$this->assoc_player->buff_retr('immune') && $this->assoc_player->stats_get(Model_Player::MP_STAT_ZOMBIFY) <= 0 && (mt_rand(0,100) <= 5)) {
            $this->assoc_player->stats_set(Model_Player::MP_STAT_ZOMBIFY, 5);
            $this->assoc_player->log()->add(new Model_Log_Types_Text(null, null, 'Deine Bisswunde sieht aber gar nicht gut aus... offenbar hast du dich mit dem Zombievirus infiziert!'));
        }
        return parent::tick();
    }
}
