<?php defined('SYSPATH') OR die('No direct access allowed.');

abstract class Model_Gamelayer_Logic extends Model_Gamelayer_Io {

	const MGLS_Health =  1;
	const MGLS_Energy =  2;
	const MGLS_Hunger =  4;
	const MGLS_Thirst =  8;
	const MGLS_Drunk  = 16;
	const MGLS_Sleepy = 32;


    public function recalculate_flow() {
        if ($this->timeflow() != 1) return;
        $steps = array(15,30,60,120,300,600,900);

        $sum = 0;
        $players = $this->players(true);

        if (count($players) == 0)
            return;

        foreach ($players as $p)
            $sum += $p->vote_time();

        //Reflow
        $this->reflow_ticks($steps[(int)round($sum/count($players))]);
    }

    /**
     * @param number $location
     * @param Model_Combat_Actor $obj
     */
    public function register_ghul($location, $obj) {
        $this->set['gamedata']->ghuls[] = array('location' => $location, 'data' => $obj);
    }

    public function unregister_ghul($id) {
        unset($this->set['gamedata']->ghuls[$id]);
    }

	/**
	 * @param $lid
	 * @return Model_Combat_Zombies_Ghul[]
	 */
    public function get_ghuls($lid) {
        $ret = [];

        foreach ($this->set['gamedata']->ghuls as $k => $gob)
            if (mt_rand(0,100) < (($gob['location'] == $lid) ? 30 : 5)) {
                $ret[$k] = $gob['data'];
                break;
            }

        return $ret;
    }

	public function tumble($pid = null) {
		if (!$this->get_player($pid)) return false;
        return (mt_rand(15, 100) <= $this->get_player($pid)->stats_get(Model_Player::MP_STAT_DRUNK));
	}

	final public function mass_consume($data, $callbacks = NULL) {
		if ($data === NULL) return false;
			
		//Check, if all items are available
		foreach ($data as $class => $count)
			if (Tool_Scripts::count_available_items($class) < $count) return false;
		
		//Callbacks
		if ($callbacks)
			foreach ($data as $class => $count) if (isset($callbacks[$class]))
			{
				$tempcount = 0;
				foreach (Tool_Scripts::available_items($class) as $item) if ($count > 0)
					if ($callbacks[$class]($item)) $tempcount++;
				if ($tempcount < $count) return false;
			}	
		
		//Consume items
		foreach ($data as $class => $count)
			if (Tool_System::instance_of($class, 'Model_Items_Abstract_Ammo')) {
				/** @var $belt Model_Items_Ammobelt[] */
                if ((!$belt = Tool_Scripts::available_items('Model_Items_Ammobelt'))) return false;
				$belt[0]->get($class, $count);
			}
			else foreach (Tool_Scripts::available_items($class) as $item) if ($count > 0) if ((isset($callbacks) && isset($callbacks[$class]) && $callbacks[$class]($item)) || !isset($callbacks) || !isset($callbacks[$class]))
			{
				$item->consume();
				$count--;
			}
			
		return true;
	}
	
	//Returns TRUE, if given UIN can be used as an item (based on the inventory the item currently resides in)
	final public function item_available($uin) {
        /**
         * @global $player Model_Player
         */
        global $player;
		
		//Check player inventory and location inventory
		return ($player->inventory()->has($uin) || (($this->location()) ? $this->location()->inventory()->has($uin) : false));
	}	
	
	//Returns stat value, or alters one (or more) of them;
	//$mask controls wich values will be altered; if $alteration is not set, $mask will determin wich value is returned
	final public function stats($mask, $alteration = NULL) {
        /**
         * @global $player Model_Player
         */
        global $player;
		
		if (!$player->alive()) return false;
		
		if ($alteration === NULL)
		{
			
			if ($mask & self::MGLS_Health) return $player->stats_get(Model_Player::MP_STAT_HEALTH);
			if ($mask & self::MGLS_Energy) return $player->stats_get(Model_Player::MP_STAT_ENERGY);
			if ($mask & self::MGLS_Hunger) return $player->stats_get(Model_Player::MP_STAT_HUNGER);
			if ($mask & self::MGLS_Thirst) return $player->stats_get(Model_Player::MP_STAT_THIRST);
			if ($mask & self::MGLS_Drunk ) return $player->stats_get(Model_Player::MP_STAT_DRUNK);
			if ($mask & self::MGLS_Sleepy) return $player->stats_get(Model_Player::MP_STAT_SLEEPY);

            return true;
		}
		elseif ($player->alive())
		{
			$array = Array();
			
			if ($mask & self::MGLS_Health) {$array[] = Model_Player::MP_STAT_HEALTH; $array[] = $alteration; }
			if ($mask & self::MGLS_Energy) {$array[] = Model_Player::MP_STAT_ENERGY; $array[] = $alteration; }
			if ($mask & self::MGLS_Hunger) {$array[] = Model_Player::MP_STAT_HUNGER; $array[] = $alteration; }
			if ($mask & self::MGLS_Thirst) {$array[] = Model_Player::MP_STAT_THIRST; $array[] = $alteration; }
			if ($mask & self::MGLS_Drunk ) {$array[] = Model_Player::MP_STAT_DRUNK;  $array[] = $alteration; }
			if ($mask & self::MGLS_Sleepy) {$array[] = Model_Player::MP_STAT_SLEEPY; $array[] = $alteration; }
			
			$player->stats_modify($array);
			return true;
		}

        return false;
	}

	final public function statchanges($mask) {
        /**
         * @global $player Model_Player
         */
		global $player;
		
		if ($mask & self::MGLS_Health) return $player->stats_buffs(Model_Player::MP_STAT_HEALTH);
		if ($mask & self::MGLS_Energy) return $player->stats_buffs(Model_Player::MP_STAT_ENERGY);
		if ($mask & self::MGLS_Hunger) return $player->stats_buffs(Model_Player::MP_STAT_HUNGER);
		if ($mask & self::MGLS_Thirst) return $player->stats_buffs(Model_Player::MP_STAT_THIRST);
		if ($mask & self::MGLS_Drunk ) return $player->stats_buffs(Model_Player::MP_STAT_DRUNK);
		if ($mask & self::MGLS_Sleepy) return $player->stats_buffs(Model_Player::MP_STAT_SLEEPY);

        return 0;
	}
	
	public function requirements($energy, $items, $callbacks = NULL) {
        /**
         * @global $game Model_Game
         * @global $player Model_Player
         */
        global $game, $player;
			
		//Check if player has sufficiant energy
		if ($game->stats(Model_Game::MGLS_Energy) < $energy)
		{
			
			$player->log()->add(new Model_Log_Types_Text(null, null, 'Dazu fehlt dir im Moment die Energie.'));
			return false;
		}
	
		//Try to consume ressources
		if (!$game->mass_consume($items, $callbacks))
		{
			$player->log()->add(new Model_Log_Types_Text(null, null, 'Dir fehlen die Materialien für dieses Projekt.'));
			return false;
		}
	
		$game->stats(Model_Game::MGLS_Energy, -$energy);
		return true;
	}
}
