<?php defined('SYSPATH') OR die('No direct access allowed.');

abstract class Model_Gamelayer_Process extends Model_Gamelayer_Exec {

	/**
	 * @param null $lid
	 * @return Model_Places_Abstract_Place
	 */
	abstract public function location($lid = NULL);

	abstract public function paused();
	abstract public function is_alive();

	protected static $now_is_real_time = false;

    abstract public function recalculate_flow();

	final protected function process_step() {
        //Jump processing time
        $this->set['gamedata']->timing->last_point += $this->set['gamedata']->timing->tick_lenght;

        //MAINTENANCE
        if (Tool_Events::maintenance($this->set['gamedata']->timing->last_point))
            return;

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

    final public function fast_forward($ticks = 0) {
        /** @global Model_Euser $user */
        global $user;

        $original_time = $this->set['gamedata']->timing->last_point;

        for ($i = 0; $i < $ticks; $i++)
            $this->process_step();

        $this->set['gamedata']->timing->last_point = $original_time;

        //Restore active player
        global $player;
        if ($user)
            $player = $this->get_player($user->uid());
    }

	final protected function process() {
        /** @global Model_Euser $user */
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
                $this->set['gamedata']->uin->get($active_player, 'Model_Player')->get_status()->rebuild();
			}

			//Call ticks until present time is reached
			while ($this->is_alive() && ($this->set['gamedata']->timing->last_point + $this->set['gamedata']->timing->tick_lenght) <= time())
			{				
                $this->process_step();
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

        global $player;
        $bfp = $player;

		//Find and run preticks
		$active_locations = [];
        foreach ($this->playable_entities() as $plentity)
            if ($plentity->can(Interface_Plentity::IC_TRIGGER_LOCATION_TICKS))
                $active_locations[$plentity->location_class()] = true;
        $active_locations = array_keys($active_locations);

		foreach ($active_locations as $lid)
		    if ($this->location($lid)) {
                $this->location($lid)->pretick();
                foreach ($this->location($lid)->inventory()->get('Interface_Tickable') as $item)
                    /** @var $item Interface_Tickable */
                    $item->tick($lid, Interface_Tickable::IT_TYPE_LOCATION);
            }

		
		//Run player and NPC ticks
		foreach ($this->playable_entities(true) as $pl) {
			$player = $pl;
			
			if ($player->get_status()->alive()) {
                // Tick items
                foreach ($player->inventory()->get('Interface_Tickable') as $item)
                    /** @var $item Interface_Tickable */
                    $item->tick($player->id(), !Tool_Scripts::is_npc($player) ? Interface_Tickable::IT_TYPE_PLAYER : Interface_Tickable::IT_TYPE_NPC);

                foreach ($player->location()->inventory()->get('Interface_Tickable') as $item)
                    /** @var $item Interface_Tickable */
                    $item->tick($player->location_class(), false);

                $player->tick();
                $player->location()->tick(!Tool_Scripts::is_npc($player) ? Interface_Tickable::IT_TYPE_PLAYER : Interface_Tickable::IT_TYPE_NPC);
			}
		}

        $player = $bfp;

        //Post-tick events
        foreach ($this->npcs() as $pl) {
            global $player;
            $player = $pl;

            $player->ai();
        }

        $player = $bfp;
	}
}
