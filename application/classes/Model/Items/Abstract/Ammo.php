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
        if (isset(static::$boni[$player->job()])) {
            $lv = $player->job(false);
            while ($lv > 0 && !isset(static::$boni[$player->job()][$lv])) $lv--;
            $bonus = isset(static::$boni[$player->job()][$lv]) ? static::$boni[$player->job()][$lv] : 1;
        }

		if ($num === null && isset(static::$boni[$player->job()]))
			$this->count = ceil($this->count * $bonus);
	}

    public function remoteTake($pid, $silent = false) {
        global $game, $player;

        $p = $game->get_player($pid);

        /** @var $belt Model_Items_Ammobelt[] */
        $belt = $p->inventory()->get('Model_Items_Ammobelt');
        if (!$belt) {
            if (!$silent) $player->log()->add(new Model_Log_Types_Text(null, null, 'Dein Freund benötigt einen Munitionsgürtel, um diesen Gegenstand mitführen zu können.'));
            return false;
        }

        $belt[0]->add($this);
        return true;
    }
	
	public function take($silent = false) {
        /**
         * @global $player Model_Player
         */
        global $player;

        /** @var $belt Model_Items_Ammobelt[] */
		$belt = $player->inventory()->get('Model_Items_Ammobelt');
		if (!$belt) {
			if (!$silent) $player->log()->add(new Model_Log_Types_Text(null, null, 'Du benötigst einen Munitionsgürtel, um diesen Gegenstand mitführen zu können.'));
			return false;			
		}

		$belt[0]->add($this);
        return true;
	}
}	