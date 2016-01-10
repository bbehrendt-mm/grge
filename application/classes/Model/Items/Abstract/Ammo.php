<?php defined('SYSPATH') OR die('No direct access allowed.');

abstract class Model_Items_Abstract_Ammo extends Model_Items_Abstract_Stackable {
	
	protected static $boni = Array(1020 => Array(1 => 1, 2 => 1, 3 => 1, 4 => 1.15, 5 => 1.25, 6 => 1.25));
	
	public function __construct($num = null) {
        /**
         * @global $player Model_Player
         */
        global $player;
		parent::__construct($num);

        $bonus = 1;
        if ($player && !Tool_Scripts::is_npc() && isset(static::$boni[$player->job()])) {
            $lv = $player->job(false);
            while ($lv > 0 && !isset(static::$boni[$player->job()][$lv])) $lv--;
            $bonus = isset(static::$boni[$player->job()][$lv]) ? static::$boni[$player->job()][$lv] : 1;
        }
        $this->count = ceil($this->count * $bonus);
	}
	
	public function take($silent = false) {
        return true;
	}
}	