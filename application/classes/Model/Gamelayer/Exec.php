<?php defined('SYSPATH') OR die('No direct access allowed.');

abstract class Model_Gamelayer_Exec extends Model_Gamelayer_Storage {

	final public function start($mode, $time_mode, $speed, $contest_id = null, $name = null) {
		//Create initial game data and timestamp
		$timestamp = time();
		
		//Create game entry in DB
		$gameid = DB::insert('games', array('timestamp', 'gamedata'))->values(array($timestamp,  ''))->execute();
		$gameid =  $gameid[0];
		
		//Return false if game could not be created
		if (!$gameid) return false;
		
		//Fill local set
		$this->set['timestamp'] = $timestamp;
		$this->set['gameid'] = $gameid;
		
		//Initialize game
		try 
		{
			new Init_Game($this, $this->set['gamedata'], $this->set['gameid'], $mode, $time_mode, $speed, $contest_id, $name);
            Tool_Events::handle_event_triggers($timestamp);
            $this->write();
		}
		catch (Exception $e)
		{
			DB::delete('games')->where('gameid', '=', $this->set['gameid'])->execute();
			DB::delete('games_cloud')->where('gameid', '=', $this->set['gameid'])->execute();	
			throw $e;							
		}	
		return $this->set['gameid'];
	}

    /**
     * @param bool $limit_alive
     *
     * @return Model_Player[]
     */
    abstract public function players($limit_alive = true): array;

    /**
     * @param bool $limit_alive
     *
     * @return Interface_Plentity[]
     */
    abstract public function npcs($limit_alive = true): array;

    /**
     * @param bool $limit_alive
     *
     * @return Interface_Plentity[]
     */
    abstract public function playable_entities($limit_alive = true): array;

	public function join($sub, $level, $contest_id): bool
    {
	    if ($this->read_only) return false;

        if (isset($this->set['gamedata']->players[Globals::CurrentUserF()->uid()])) return false;
        /** @noinspection PhpParamsInspection */
        new Init_Player($this, $this->set['gamedata'], Globals::CurrentUserF()->uid(), Globals::CurrentUserF()->name(), $sub, $level);
	
		if (!DB::insert('xref_game_player', array('gameid', 'uid'))->values(array($this->set['gameid'], Globals::CurrentUserF()->uid()))->execute()) {
			$this->retire(Globals::CurrentUserF()->uid());
			return false;
		}

        // Chat room
        if ($this->config('modules.multiplayer'))
            Controller_Chat::register_user(Globals::PrimaryPlayerF()->id(),$this->id());

        /** @var Model_Player $p */
        foreach ($this->players(true) as $p) if ($p->id() !== Globals::PrimaryPlayerF()->id())
            $p->log()->add(new Model_Log_Types_String(null, ':name ist soeben der Partie beigetreten.', array(':name' => Globals::PrimaryPlayerF()->name())));
		
		//Create contest ranking
		if ($contest_id)
			if (!DB::insert('contests', array('contest_id', 'user_id', 'game_id', 'points'))->values(array($contest_id, Globals::CurrentUserF()->uid(), $this->set['gameid'], 0))->execute())
				$contest_id = null;
		
		return true;
	}

    /**
     * Returns the game name, or null if no name is set
     * @return null|string
     */
    public function name(): ?string
    {
        return $this->set['gamedata']->head->name ?? null;
    }

    public function slots() {
        if (!$this->config('modules.multiplayer')) return false;

        $num = DB::select('slots')->from('multiplayer_lobby')->where('gameid', '=', $this->set['gameid'])->execute()->as_array();
        if (count($num) !== 1)
            return 0;
        else return (int)$num[0]['slots'];
    }

	public function points($pid = null) {	
		if ($pid)
            $player = $this->get_player($pid);
        else $player = Globals::PrimaryPlayerF();
		
		if (!$pid) $duration = $this->duration();
		elseif ($player->get_points() !== null && !$player->get_status()->alive())
            return $player->get_points();
        else $duration = $player->get_lifetime();
		
		if (isset($this->set['gamedata']->head->contest) && $this->set['gamedata']->head->contest) {
			$raw = Kohana::$config->load('contests.' . $this->set['gamedata']->head->contest['id']);

			$t = time();
			if ($this->set['gamedata']->head->contest['start'] < $t && $this->set['gamedata']->head->contest['end'] > $t)
				$this->set['gamedata']->head->contest['points'] = $raw['result']($this);
			return $this->set['gamedata']->head->contest['points'];
		}

        /** @noinspection PhpUndefinedMethodInspection */
        $tmp = $this->set['gamedata']->maps['main']->get_by_fixed_id(2)->get_map_points();
		$ac_p = 0;
		$ac_p += $this->config('ranking.points.zombie_kills.offset') + floor($player->achievements()->get_achievements(Model_Achievement::MA_KILLED_ZOMBIES) * $this->config('ranking.points.zombie_kills.factor'));
		$ac_p += $this->config('ranking.points.survival.offset') + floor(Tool_Numerics::duration_to_points($duration) * $this->config('ranking.points.survival.factor'));
		$ac_p += $this->config('ranking.points.home.offset') + floor((($tmp*($tmp-$this->config('ranking.points.home.threshold')))/$this->config('ranking.points.home.stretch')) * $this->config('ranking.points.home.factor'));
		
		return max(0, $ac_p);	
	}
	
	abstract public function duration();

    public function is_retired($uid): bool
    {
        return isset($this->set['gamedata']->graveyard[$uid]);
    }

    public function register_death($uid): void
    {
        if ($this->read_only) return;
        $p = $this->get_player($uid);
        if (!$p) return;

        if ($this->config('game.lobby.persistent'))
            $lobby_open = DB::select([DB::expr('COUNT(`gameid`)'), 'games'])->from('multiplayer_lobby')->where('gameid', '=', $this->set['gameid'])->and_where('slots', '>', 0)->execute()->get('games') > 0;
        else
            $lobby_open = DB::delete('multiplayer_lobby')->where('gameid', '=', $this->set['gameid'])->and_where('slots', '>', 0)->execute() > 0;

        if ($lobby_open && $p->get_lifetime() < 288 && Kohana::$config->load('build.version.stage') < 3)
            DB::insert('mp_lockouts', array('uid', 'timestamp'))->values(array($uid, time()))->execute();
    }
    
	public function retire($uid, $as_batch = false): bool
    {
        if ($this->is_retired($uid))
            return false;

        $p = $this->get_player($uid);
        if (!$p) return false;

		//Create ranking entry if game is rankable and player has more than zero points
        $p->expire($this->set['gamedata']->head->season, $this->set['gameid'],
            $this->set['gamedata']->head->rankable && !(isset($this->set['gamedata']->head->contest) && $this->set['gamedata']->head->contest), $this->set['gamedata']->timing->game_start, $this->set['gamedata']->timing->last_point);

        $this->set['gamedata']->graveyard[$uid] = $this->setting_mode(11000) ? $p->get_points() : $p->get_lifetime();

        if (!$this->read_only) {
            DB::delete('xref_game_player')->where('uid', '=', $uid)->execute();

            if ($p->get_lifetime() >= 288 && $p->get_braincoins())
                Model_User::award_coins($uid, $p->get_braincoins());

            if (!$as_batch) {
                $this->check_players();
                $this->write();
            }
        }

		return true;
	}

    /**
     * @param null|number $pid
     *
     * @return null|Model_Player
     */
    abstract public function get_player($pid = NULL): ?Interface_Plentity;

    /**
     * @param string $adress Configuration adress
     * @param mixed $value If set, the configuration will be overwritten; otherwise, the current value will be returned
     * @return mixed|null
     */
    abstract public function config($adress, $value = null);

	public function check_players(): void
    {
	    if ($this->read_only) return;

        if (count($this->set['gamedata']->players) === count($this->set['gamedata']->graveyard)) {
			
			//Create contest ranking
			if (isset($this->set['gamedata']->head->contest) && $this->set['gamedata']->head->contest)
				DB::update('contests')->set(array('points' => $this->points(), 'game_id' => -1))->where('game_id', '=', $this->set['gameid'])->execute();

            //Create MP ranking
            if (($this->set['gamedata']->head->rankable && !(isset($this->set['gamedata']->head->contest) && $this->set['gamedata']->head->contest))
                && $this->config('modules.multiplayer')
            ) {
                $sum = 0;

                foreach ($this->set['gamedata']->graveyard as $points)
                    if ($this->setting_mode(11000))
                        $sum = max($sum, $points);
                    else $sum += $points;
                if ($sum > 0)
                    DB::insert('ranking_mp', array('season', 'board', 'gameid', 'name', 'points'))->values(array($this->set['gamedata']->head->season, $this->set['gamedata']->head->mode, $this->set['gameid'], $this->set['gamedata']->head->name, $sum))->execute();
            }

			//Delete game entry and cloud from DB
			$this->purge();
		}
	}

    public function delete_lobby(): void
    {
        if (!$this->read_only)
	        DB::delete('multiplayer_lobby')->where('gameid', '=', $this->set['gameid'])->execute();
    }

	public function purge(): void
    {
        if ($this->read_only) return;

	    // Chat room
        if ($this->config('modules.multiplayer'))
            Controller_Chat::purge_room($this->id());
	    DB::delete('games')->where('gameid', '=', $this->set['gameid'])->execute();
        $this->delete_lobby();
		$this->set['gamedata']->uin->clean();
		Model_Combat_Handler::delete_game($this->set['gameid'], $this->set['gamedata']->head->season);
	}
	
	public function setting_mode($compare = NULL) {
		if ($compare === NULL) return $this->set['gamedata']->head->mode;
		else return ($this->set['gamedata']->head->mode === $compare);
	}
}