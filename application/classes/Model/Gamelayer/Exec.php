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
			Model_Chat::create_room($this->chatroom(), false);
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

    abstract public function players($limit_alive = true);

	public function join($sub, $level, $contest_id) {
		global $user, $player;

        if (isset($this->set['gamedata']->players[$user->uid()])) return false;
        Model_Chat::grant_access($this->chatroom(), $user->uid(), $user->name());

		new Init_Player($this, $this->set['gamedata'], $user->uid(), $user->name(), $sub, $level);
	
		if (!DB::insert('xref_game_player', array('gameid', 'uid'))->values(array($this->set['gameid'], $user->uid()))->execute()) {
			$this->retire($user->uid());
			return false;
		}

        /** @var Model_Player $p */
        foreach ($this->players(true) as $p) if ($p->id() != $player->id())
            $p->log()->add(new Model_Log_Types_Text(null, null, ':name ist soeben der Partie beigetreten.', array(':name' => $player->name())));
		
		//Create contest ranking
		if ($contest_id)
			if (!DB::insert('contests', array('contest_id', 'user_id', 'game_id', 'points'))->values(array($contest_id, $user->uid(), $this->set['gameid'], 0))->execute())
				$contest_id = null;
		
		return true;
	}

    /**
     * Returns the game name, or null if no name is set
     * @return null|string
     */
    public function name() {
        return (isset($this->set['gamedata']->head->name)) ? $this->set['gamedata']->head->name : null;
    }

    public function slots() {
        if (!$this->config('modules.multiplayer')) return false;

        $num = DB::select('slots')->from('multiplayer_lobby')->where('gameid', '=', $this->set['gameid'])->execute()->as_array();
        if (count($num) != 1)
            return 0;
        else return (int)$num[0]['slots'];
    }

	public function points($pid = null) {	
		if ($pid)
            $player = $this->get_player($pid);
        else global $player;
		
		if (!$pid) $duration = $this->duration();
		elseif (!$player->alive() && $player->get_points() !== null)
            return $player->get_points();
        else $duration = $this->get_player($pid)->get_lifetime();
		
		if (isset($this->set['gamedata']->head->contest) && $this->set['gamedata']->head->contest) {
			$raw = Kohana::$config->load('contests.' . $this->set['gamedata']->head->contest['id']);
			if ($this->set['gamedata']->head->contest['start'] < time() && $this->set['gamedata']->head->contest['end'] > time())
				$this->set['gamedata']->head->contest['points'] = $raw['result']($this);
			return $this->set['gamedata']->head->contest['points'];
		}

		$tmp = $this->set['gamedata']->maps['main']->get_by_fixed_id(2)->get_map_points();
		$ac_p = 0;
		$ac_p += $this->config('ranking.points.zombie_kills.offset') + floor($player->achievements()->get_achievements(Model_Achievement::MA_KILLED_ZOMBIES) * $this->config('ranking.points.zombie_kills.factor'));
		$ac_p += $this->config('ranking.points.survival.offset') + floor(Tool_Numerics::duration_to_points($duration) * $this->config('ranking.points.survival.factor'));
		$ac_p += $this->config('ranking.points.home.offset') + floor((($tmp*($tmp-$this->config('ranking.points.home.threshold')))/$this->config('ranking.points.home.stretch')) * $this->config('ranking.points.home.factor'));
		
		return max(0, $ac_p);	
	}
	
	abstract public function duration();
	
	public function retire($uid, $as_batch = false) {
        if (isset($this->set["gamedata"]->graveyard[$uid]))
            return false;

        Model_Chat::revoke_access($this->chatroom(), $uid);

		//Create ranking entry if game is rankable and player has more than zero points
		$this->get_player($uid)->expire($this->set['gamedata']->head->season, $this->set['gameid'], ($this->set['gamedata']->head->rankable && !(isset($this->set['gamedata']->head->contest) && $this->set['gamedata']->head->contest)), $this->set['gamedata']->timing->game_start, $this->set['gamedata']->timing->last_point);

        $this->set["gamedata"]->graveyard[$uid] = $this->setting_mode(11000) ? $this->get_player($uid)->get_points() : $this->get_player($uid)->get_lifetime();
		
		DB::delete('xref_game_player')->where('uid', '=', $uid)->execute();

        $lobby_data = DB::select('gameid')->from('multiplayer_lobby')->where('gameid', '=', $this->set['gameid'])->and_where('slots', '>', 0)->execute()->as_array();
        if (count($lobby_data) > 0 && $this->get_player($uid)->get_lifetime() < 288 && Kohana::$config->load('build.version.stage') < 3)
            DB::insert('mp_lockouts', array('uid', 'timestamp'))->values(array($uid, time()))->execute();

        if ($this->get_player($uid)->get_lifetime() >= 288 && $this->get_player($uid)->get_braincoins())
            Model_User::award_coins($uid, $this->get_player($uid)->get_braincoins());

        if (!$as_batch) {
            $this->check_players();
            $this->write();
        }
		
		return true;
	}

    /**
     * @param null|number $pid
     * @return null|Model_Player
     */
    abstract public function get_player($pid = NULL);

    /**
     * @param string $adress Configuration adress
     * @param mixed $value If set, the configuration will be overwritten; otherwise, the current value will be returned
     * @return mixed|null
     */
    abstract public function config($adress, $value = null);

	public function check_players() {
		if (count($this->set["gamedata"]->players) == count($this->set["gamedata"]->graveyard)) {
			
			//Create contest ranking
			if (isset($this->set['gamedata']->head->contest) && $this->set['gamedata']->head->contest)
				DB::update('contests')->set(array('points' => $this->points(), 'game_id' => -1))->where('game_id', '=', $this->set['gameid'])->execute();

            //Create MP ranking
            if ($this->config('modules.multiplayer') && ($this->set['gamedata']->head->rankable && !(isset($this->set['gamedata']->head->contest) && $this->set['gamedata']->head->contest))) {
                $sum = 0;

                foreach ($this->set["gamedata"]->graveyard as $points)
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

    public function delete_lobby() {
        DB::delete('multiplayer_lobby')->where('gameid', '=', $this->set['gameid'])->execute();
    }
	
	public function purge() {
        Model_Chat::delete_room($this->chatroom());

        DB::delete('games')->where('gameid', '=', $this->set['gameid'])->execute();
        $this->delete_lobby();
		$this->set['gamedata']->uin->clean();
	}
	
	public function setting_mode($compare = NULL) {
		if ($compare === NULL) return $this->set['gamedata']->head->mode;
		else return ($this->set['gamedata']->head->mode == $compare); 
	}
}