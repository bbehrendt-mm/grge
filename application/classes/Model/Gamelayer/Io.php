<?php defined('SYSPATH') OR die('No direct access allowed.');

abstract class Model_Gamelayer_Io extends Model_Gamelayer_Process {

    private $config_defaults = array(
        'game.config.map'       => 'default', //CONFIG UPDATE: 6 => 6.5
        'game.config.itemset'   => 'default', //CONFIG UPDATE: 6 => 6.5
        'game.config.spawn'     => 'default'
    );

	/**
	 * Returns time flow mode
	 */
	public function timeflow() {
		return $this->set['gamedata']->timing->flow_mode;
	}

	public function season() {
		return $this->set['gamedata']->head->season;
	}
	
	/**
	 * Check if game is paused
	 * @see Model_Gamelayer_Process::paused()
	 */
	public function paused() {
		if (!isset($this->set['gamedata']->head->paused)) $this->set['gamedata']->head->paused = false;
		return $this->set['gamedata']->head->paused;
	}
	
	/**
	 * Returns pause lock time
	 */
	public function pauselock() {
		if (!isset($this->set['gamedata']->head->pauselock)) $this->set['gamedata']->head->pauselock = 0;
		return $this->set['gamedata']->head->pauselock;
	}

    public function getDaytimeOffset() {
        return $this->set['gamedata']->head->daytime_offset;
    }
	
	/**
	 * Pauses game
	 */
	public function pause() {
		$this->set['gamedata']->head->pauselock = time();
		$this->set['gamedata']->head->paused = true;
	}
	
	/**
	 * Unpauses game
	 */
	public function unpause() {
		$this->set['gamedata']->head->pauselock = time();
		$this->set['gamedata']->timing->last_point = time();
		$this->set['gamedata']->head->paused = false;
	}
	
	/**
	 * Changes tick speed
	 * @param number $new New tick speed (in seconds)
	 */
	public function reflow_ticks($new) {
		$this->set['gamedata']->timing->tick_lenght = $new;
	}
		
	/**
	 * Returns true if there are any players still alive in this game
	 * @see Model_Gamelayer_Process::is_alive()
	 */
	public function is_alive() {
		$tmp = false;
		foreach (array_keys($this->set['gamedata']->players) as $remote_player) $tmp = $tmp || ($this->get_player($remote_player) && $this->get_player($remote_player)->get_status()->alive());
		
		return $tmp;
	}
	
	/**
	 * Returns true if this game can be ranked
	 */
	public function is_rankable() {
		return $this->set['gamedata']->head->rankable;
	}
	
	/**
	 * Disabled ranking for this game
	 */
	public function unrank() {
		$this->set['gamedata']->head->rankable = false;
	}
	
	/**
	 * Returns the exact unix timestamp for the next tick
	 * @return number
	 */
	final public function next_tick() {
		return $this->set['gamedata']->timing->last_point + $this->set['gamedata']->timing->tick_lenght;
	}	
	
	/**
	 * Returns game duration
	 * @see Model_Gamelayer_Exec::duration()
	 * @return number
	 */
	final public function duration() {
		return $this->set['gamedata']->head->ticks;
	}
	
	/**
	 * Gets or sets a configuration value
	 * @param string $adress Configuration adress
	 * @param mixed $value If set, the configuration will be overwritten; otherwise, the current value will be returned
     * @return mixed|null
     */
	final public function config($adress, $value = null) {
		if ($value === null) {
            if (!isset($this->set['gamedata']->head->params[$adress])) {
                if (isset($this->config_defaults[$adress]))
                    return $this->config($adress, $this->config_defaults[$adress]);
                else return null;
            }

            return $this->set['gamedata']->head->params[$adress];
        }
		else return $this->set['gamedata']->head->params[$adress] = $value;
	}
	
	/**
	 * Returns the exact unix timestamp of the last tick (i.e. the current ingame game time)
	 */
	final public function now() {
		return static::$now_is_real_time ? time() : $this->set['gamedata']->timing->last_point;
	}
	
	/**
	 * Returns the current tick length, or overwrites it without regard of pauselock
	 * @param number $new New value; omit to return current value
     * @return null|number
	 */
	final public function tick_length($new = NULL) {
		if ($new === NULL) return $this->set['gamedata']->timing->tick_lenght;
		else return $this->set['gamedata']->timing->tick_lenght = $new;
	}
	
	/**
	 * Returns contest data, or null if this is not a contest game
	 * @return NULL
	 */
	final public function contest_data() {
		if (!isset($this->set['gamedata']->head->contest)) return null;
		return $this->set['gamedata']->head->contest;
	}
	
	/**
	 * Returns all active contests
	 * @return mixed[]
	 */
	public static function get_active_contests() {
		$contests = Kohana::$config->load('contests');
		$tmp = Array();
		foreach ($contests as $id => $contest) if ($contest['start'] < time() && $contest['end'] > time()) $tmp[$id] = $contests[$id];
		return $tmp;
	}
	
	/**
	 * Returns contest data of a contest specified by $cid
	 * @param string $cid
	 * @return null|mixed
	 */
	public static function get_contest_data($cid) {
		if (!$cid) return null;
		$contests = Model_Game::get_active_contests();
		if (isset($contests[$cid])) {
			$data = $contests[$cid];
			$data['id'] = $cid;
			unset($data['result']);
			return $data;
		}
		else return null;
	}
	
	/**
	 * Returns this games UIN Manager
	 * @return Model_Uinmanager
	 */
	final public function uin() {
		return $this->set['gamedata']->uin;
	}

    /**
     * @param null $lid Location ID to determine map, null to get main map
     * @return Model_Map_Abstract|null
     */
    final public function map($lid = null) {
        if (!($key = $this->mapid($lid)))
            return null;
        else return $this->set['gamedata']->maps[$key];
    }

    /**
     * @param null $lid Location ID to determine map, null to get main map
     * @return string|null
     */
    final public function mapid($lid = null) {
        if ($lid === null)
            return 'main';
        else foreach ($this->set['gamedata']->maps as $id => $map)
            /** @var $map Model_Map_Abstract */
            if ($map->has_location($lid))
                return $id;
        return null;
    }

    /**
     * Returns the main map
     * @return Model_Map_Abstract
     */
    final public function map_main() {
        return $this->set['gamedata']->maps['main'];
    }

    /**
     * Returns all locations
     * @return int[]
     */
    final public function locations() {
        $ret = array();
        foreach ($this->set['gamedata']->maps as $map)
            /** @var $map Model_Map_Abstract */
            $ret = array_merge_recursive($map->get_locations(null, true),$ret);

        return $ret;
    }

    /**
     * Deletes all maps
     */
    final public function reset_maps($map_cfg) {
        $this->config('game.config.map', $map_cfg);
        $this->set['gamedata']->maps = array();
        $this->set['gamedata']->maps['main'] = Model_Map_Abstract::factory($map_cfg);
        $this->set['gamedata']->maps['main']->auto_init();
    }

    /**
     * @return Model_Map_Abstract[]
     */
    final public function maps() {
        return array_values($this->set['gamedata']->maps);
    }

	/**
	 * Returns a location by its ID
     * @param $lid null|number Optional location id, when missing player location is assumed
	 * @see Model_Gamelayer_Process::location()
     * @return Model_Places_Abstract_Place|null
	 */
	final public function location($lid = NULL) {
        /**
         * @global $player Model_Player
         */
        global $player;
	
		$location = ($lid !== NULL) ? $lid : $player->location_class();

        if ($location < 0)
            return $this->set['gamedata']->maps['main']->get_by_fixed_id(-$location);
		else return $this->set['gamedata']->uin->get($location, 'Model_Places_Abstract_Place');
	}

    /**
     * @param $mapid
     * @param $sublocation
     * @return bool|number
     */
    final public function register_map($mapid, $sublocation, $map_cfg = null) {
        if (isset($this->set['gamedata']->maps[$mapid]))
            return false;

        $this->set['gamedata']->maps[$mapid] = Model_Map_Abstract::factory(($map_cfg == null) ? $this->config('game.config.map') : $map_cfg, $sublocation);
        $this->set['gamedata']->maps[$mapid]->auto_init();
        return $this->set['gamedata']->maps[$mapid]->resolve_fixed_id(1);
    }

    /**
     * Returns the chat room name for this game
     * @return string
     */
    final public function chatroom() {
        return 'grg_private_' . $this->set['gameid'];
    }

    /**
     * Returns the player object associated with $pid; if $pid is not passed, the active player will be returned
     * @param number|string|null $pid
     * @return Model_Player|Interface_Plentity|null
     */
    public function get_player($pid = NULL) {
    	/** @global $user Model_Euser */
        global $user;
    	if ($pid === NULL) {
            if ($user) $pid = $user->uid();
            else return null;
        } elseif (!is_numeric($pid)) return $this->get_npc($pid);

    	if (!isset($this->set['gamedata']->players[$pid])) return null;
    	else return $this->set['gamedata']->uin->get($this->set['gamedata']->players[$pid], 'Model_Player');
    }
    
    /**
     * Returns a list of all available players
     * @param boolean $limit_alive Set true if you want only living players to be returned (default: true)
     * @return Model_Player[]
     */
    public function players($limit_alive = true) {
    	$ret = Array();
    	foreach ($this->set['gamedata']->players as $player_id => $pid)
            if (!$this->get_player($player_id)) continue;
    		elseif (!$limit_alive || $this->get_player($player_id)->get_status()->alive()) $ret[] = $this->get_player($player_id);
    
    	return $ret;
    }

    /**
     * @param $npcid
     * @return Interface_Plentity|null
     */
    public function get_npc($npcid) {
        if (!isset($this->set['gamedata']->npcs[$npcid])) return null;
        else return $this->set['gamedata']->uin->get($this->set['gamedata']->npcs[$npcid], 'Interface_Plentity');
    }

    /**
     * @param bool $limit_alive
     * @return Interface_Plentity[]
     */
    public function npcs($limit_alive = true) {
        $ret = Array();
        foreach ($this->set['gamedata']->npcs as $npc_id => $nid)
            if (!$this->get_npc($npc_id)) continue;
            elseif (!$limit_alive || $this->get_npc($npc_id)->get_status()->alive()) $ret[] = $this->get_npc($npc_id);

        return $ret;
    }

    /**
     * @param Interface_Plentity $npc
     * @param null|number|string $id
     * @return bool
     */
    public function add_npc(Interface_Plentity $npc, $id = null) {
        if ($id === null)
            $id = $npc->id();

        if ($id === null) {
            $id = count($this->set['gamedata']->npcs);
            while (isset($this->set['gamedata']->npcs["npc_$id"])) $id++;
            $id = "npc_$id";
        } elseif (is_numeric($id))
            $id = "npc_$id";

        if (isset($this->set['gamedata']->npcs[$id])) return false;

        if (!$npc->uin()) $this->uin()->set($npc);

        $npc->set_id($id);
        $this->set['gamedata']->npcs[$id] = $npc->uin();

        return $id;
    }

    /**
     * @param bool $limit_alive
     * @return Interface_Plentity[]
     */
    public function playable_entities($limit_alive = true) {
        return array_merge($this->players($limit_alive), $this->npcs($limit_alive));
    }

    /**
     * Adds a type value to the name doubling storage
     * @param string $id Reference ID
     * @param int $type Name ID
     */
    public function ndp_register($id, $type) {
        if (!isset($this->set['gamedata']->ndp[$id]))
            $this->set['gamedata']->ndp[$id] = array($type);
        elseif (!in_array($type, $this->set['gamedata']->ndp[$id]))
            $this->set['gamedata']->ndp[$id][] = $type;
    }

    /**
     * Checks, if an name ID is free
     * @param string $id Reference ID
     * @param int $type Name id
     * @return bool True, when name id is not yet registered under reference id
     */
    public function ndp_check($id, $type) {
        if (!isset($this->set['gamedata']->ndp[$id]))
            return true;
        else return !in_array($type, $this->set['gamedata']->ndp[$id]);
    }

    /**
     * Removes all name ids registered under a given reference id
     * @param string $id Reference ID
     */
    public function ndp_purge($id) {
        $this->set['gamedata']->ndp[$id] = array();
    }
}	