<?php defined('SYSPATH') OR die('No direct access allowed.');

abstract class Model_Gamelayer_Process extends Model_Gamelayer_Exec {

	/**
	 * @param null|number $lid
	 * @return Model_Places_Abstract_Place|null
	 */
	abstract public function location($lid = NULL);

	/**
     * @param null|number $lid
     * @return Model_Places_Abstract_Place
     */
    abstract public function locationF($lid = NULL);

	abstract public function paused();
	abstract public function is_alive();

    /**
     * @return Model_Events_Event[]
     */
    abstract public function get_initialized_events();

	protected static $now_is_real_time = false;

    abstract public function recalculate_flow();
    abstract public function next_tick();

	final protected function process_step() {
        //Jump processing time
        $this->set['gamedata']->timing->last_point += $this->set['gamedata']->timing->tick_lenght;

        //MAINTENANCE
        if (Tool_Events::maintenance($this->set['gamedata']->timing->last_point))
            return;

        // Trigger events
        Tool_Events::handle_event_triggers($this->next_tick());

        //Count ticks
        $this->set['gamedata']->head->ticks++;

        //Tick
        $this->tick();

        //Event ticks
        foreach ($this->get_initialized_events() as $ev)
            $ev->trigger();
        foreach ($this->get_initialized_events() as $ev)
            $ev->tick();

        //If no player is alive, stop time progression, otherwise recalculate time votes
        if (!$this->is_alive())
            $this->set['gamedata']->timing->last_point = time();
        else
            $this->recalculate_flow();
    }

    final public function fast_forward($ticks = 0) {
        $original_time = $this->set['gamedata']->timing->last_point;

        for ($i = 0; $i < $ticks; $i++)
            $this->process_step();

        $this->set['gamedata']->timing->last_point = $original_time;

        //Restore active player
        if (Globals::hasCurrentUser())
            Globals::setPrimaryPlayer($this->get_player(Globals::CurrentUserF()->uid()));
    }

	final protected function process() {
		//If no player is alive, stop time progression
		if (!$this->is_alive() || $this->paused()) {
			$this->set['gamedata']->timing->last_point = time();
			return;
		}

		try {
			//Prefetch stuff
            foreach ($this->set['gamedata']->players as $active_player) if ($active_player_obj = $this->set['gamedata']->uin->get($active_player, 'Model_Player')) {
				/** @var  Model_Player $active_player_obj */
                $active_player_obj->inventory()->prefetch();
				$active_player_obj->location()->inventory()->prefetch();

                //Rebuild buffs
                $active_player_obj->get_status()->rebuild();
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
		if (Globals::hasCurrentUser())
            Globals::setPrimaryPlayer($this->get_player(Globals::CurrentUserF()->uid()));

		static::$now_is_real_time = true;
	}
	
	protected function tick() {
        //No need to do that if player is already dead
		if (!$this->is_alive()) return;

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
			Globals::setCurrentPlayer($pl);
			
			if (Globals::CurrentPlayerF()->get_status()->alive()) {
                // Tick items
                foreach (Globals::CurrentPlayerF()->inventory()->get('Interface_Tickable') as $item)
                    /** @var $item Interface_Tickable */
                    $item->tick(Globals::CurrentPlayerF()->id(), !Tool_Scripts::is_npc(Globals::CurrentPlayerF()) ? Interface_Tickable::IT_TYPE_PLAYER : Interface_Tickable::IT_TYPE_NPC);

                if (Globals::CurrentPlayerF()->location())
                    foreach (Globals::CurrentPlayerF()->location()->inventory()->get('Interface_Tickable') as $item)
                        /** @var $item Interface_Tickable */
                        $item->tick(Globals::CurrentPlayerF()->location_class(), false);

                Globals::CurrentPlayerF()->tick();
                if (Globals::CurrentPlayerF()->location()) Globals::CurrentPlayerF()->location()->tick(!Tool_Scripts::is_npc(Globals::CurrentPlayerF()) ? Interface_Tickable::IT_TYPE_PLAYER : Interface_Tickable::IT_TYPE_NPC);
			}
		}

        //Post-tick events
        foreach ($this->npcs() as $pl) {
            Globals::setCurrentPlayer($pl);
            Globals::CurrentPlayerF()->ai();
        }

        Globals::restorePrimaryPlayer();
	}
}
