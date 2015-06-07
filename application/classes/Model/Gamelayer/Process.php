<?php defined('SYSPATH') OR die('No direct access allowed.');

abstract class Model_Gamelayer_Process extends Model_Gamelayer_Exec {

	abstract public function location($lid = NULL);
	abstract public function paused();
	abstract public function is_alive();

	protected static $now_is_real_time = false;

    abstract public function recalculate_flow();

	final protected function process() {
		global $user;

		//If no player is alive, stop time progression
		if (!$this->is_alive() || $this->paused()) {
			$this->set['gamedata']->timing->last_point = time();
			return;
		}

		try {
			//Prefetch stuff
            foreach ($this->set['gamedata']->players as $active_player) {
				$this->set['gamedata']->uin->get($active_player, 'Model_Player')->inventory()->prefetch();
				$this->set['gamedata']->uin->get($active_player, 'Model_Player')->location()->inventory()->prefetch();

                //Rebuild buffs
                $this->set['gamedata']->uin->get($active_player, 'Model_Player')->rebuild();
			}

			//Call ticks until present time is reached
			while ($this->is_alive() && ($this->set['gamedata']->timing->last_point + $this->set['gamedata']->timing->tick_lenght) <= time())
			{				
				//Jump processing time
				$this->set['gamedata']->timing->last_point += $this->set['gamedata']->timing->tick_lenght;

                //MAINTENANCE
                if (Tool_Events::maintenance($this->set['gamedata']->timing->last_point))
                    continue;

				//Count ticks
				$this->set['gamedata']->head->ticks++;
				
				//Tick
				$this->tick();

                //If no player is alive, stop time progression, otherwise recalculate time votes
                if (!$this->is_alive())
                    $this->set['gamedata']->timing->last_point = time();
                else
                    $this->recalculate_flow();
			}
		} catch (Exception $e) {
			//Resync with DB
			$this->read($this->set['gameid'], false);
			
			//Rethrow exception
			throw $e;
		}
		
		//Restore active player
        global $player;
		if ($user)
            $player = $this->get_player($user->uid());

		static::$now_is_real_time = true;
	}
	
	protected function tick() {
		//No need to do that if player is already dead
		if (!$this->is_alive()) return;

		//Find and run preticks
		$pretick = array();
		foreach (array_keys($this->set['gamedata']->players) as $remote_player) {
			global $player;
			$player = $this->get_player($remote_player);
				
			if ($player->alive()) $pretick[$player->location_class()] = method_exists($player->location(),'pretick');
		}

		foreach ($pretick as $lid => $do)
			if ($do) $this->location($lid)->pretick();
		
		//Run player ticks
		foreach (array_keys($this->set['gamedata']->players) as $remote_player) {
			global $player;
			$player = $this->get_player($remote_player);
			
			if ($player->alive()) {
                // Tick items
                foreach ($player->inventory()->get('Interface_Tickable') as $item)
                    /** @var $item Interface_Tickable */
                    $item->tick($player->id(), true);

                foreach ($player->location()->inventory()->get('Interface_Tickable') as $item)
                    /** @var $item Interface_Tickable */
                    $item->tick($player->location_class(), false);

                $player->tick();
                if (method_exists($player->location(),'tick')) $player->location()->tick();
			}
		}
	}
}
