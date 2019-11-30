<?php defined('SYSPATH') OR die('No direct access allowed.');

abstract class Model_Gamelayer_Io extends Model_Gamelayer_Process {

    private $config_defaults = array(
        // S9 - CU1 => S9 CU2
        'game.config.buffs.auto_player'         => [],
        'game.config.buffs.auto_npc'            => [],
        'game.config.event_blacklist'           => false,
        'game.bhav.daily_temperature_change'    => 0,
    );

	/**
	 * Returns time flow mode
	 */
	public function timeflow(): int {
		return $this->set['gamedata']->timing->flow_mode;
	}

	public function season(): int {
		return $this->set['gamedata']->head->season;
	}

	public function set_property(string $key, $val): void {
	    if (!isset( $this->set['gamedata']->props )) $this->set['gamedata']->props = [];

        $this->set['gamedata']->props[$key] = $val;
    }

    public function get_property(string $key, $default) {
        if (!isset( $this->set['gamedata']->props )) $this->set['gamedata']->props = [];

        return isset($this->set['gamedata']->props[$key]) ? $this->set['gamedata']->props[$key] : $default;
    }

    public function count($type, $num = null): int {
        if ($num === null)
            return $this->set['gamedata']->counters[$type] ?? 0;
        elseif (isset($this->set['gamedata']->counters[$type]))
            return $this->set['gamedata']->counters[$type] += $num;
        else return $this->set['gamedata']->counters[$type] = $num;
    }
	
	/**
	 * Check if game is paused
	 * @see Model_Gamelayer_Process::paused()
	 */
	public function paused(): bool {
		if (!isset($this->set['gamedata']->head->paused)) $this->set['gamedata']->head->paused = false;
		return $this->set['gamedata']->head->paused;
	}
	
	/**
	 * Returns pause lock time
	 */
	public function pauselock(): int {
		if (!isset($this->set['gamedata']->head->pauselock)) $this->set['gamedata']->head->pauselock = 0;
		return $this->set['gamedata']->head->pauselock;
	}

    public function getDaytimeOffset(): int {
        return $this->set['gamedata']->head->daytime_offset;
    }
	
	/**
	 * Pauses game
	 */
	public function pause(): void {
		$this->set['gamedata']->head->pauselock = time();
		$this->set['gamedata']->head->paused = true;
	}
	
	/**
	 * Unpauses game
	 */
	public function unpause(): void {
		$this->set['gamedata']->head->pauselock = time();
		$this->set['gamedata']->timing->last_point = time();
		$this->set['gamedata']->head->paused = false;
	}
	
	/**
	 * Changes tick speed
	 * @param number $new New tick speed (in seconds)
	 */
	public function reflow_ticks($new): void {
		$this->set['gamedata']->timing->tick_lenght = $new;
	}
		
	/**
	 * Returns true if there are any players still alive in this game
	 * @see Model_Gamelayer_Process::is_alive()
	 */
	public function is_alive(): bool {
		$tmp = false;
		foreach (array_keys($this->set['gamedata']->players) as $remote_player)
		    $tmp = $tmp || ($this->get_player($remote_player) !== null && $this->get_player($remote_player)->get_status()->alive());
		
		return $tmp;
	}
	
	/**
	 * Returns true if this game can be ranked
	 */
	public function is_rankable(): bool {
		return $this->set['gamedata']->head->rankable;
	}
	
	/**
	 * Disabled ranking for this game
	 */
	public function unrank(): void {
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
	final public function now(): int {
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
     * Returns all active contests
     *
     * @return mixed[]
     * @throws Kohana_Exception
     */
	public static function get_active_contests(): array {
		$contests = Kohana::$config->load('contests');
		$tmp = Array();
		$now = time();
		foreach ($contests as $id => $contest) if ($contest['start'] < $now && $contest['end'] > $now) $tmp[$id] = $contests[$id];
		return $tmp;
	}

    /**
     * Returns contest data of a contest specified by $cid
     *
     * @param string $cid
     *
     * @return null|mixed
     * @throws Kohana_Exception
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
	final public function uin(): Model_Uinmanager {
		return $this->set['gamedata']->uin;
	}

    /**
     * @param null $lid Location ID to determine map, null to get main map
     * @return Model_Map_Abstract|null
     */
    final public function map($lid = null): ?Model_Map_Abstract {
        return $this->map_by_id($this->mapid($lid));
    }

    /**
     * @param null $lid Location ID to determine map, null to get main map
     * @return Model_Map_Abstract
     * @throws RuntimeException
     */
    final public function mapF($lid = null): Model_Map_Abstract {
        $m = $this->map($lid);
        if ($m === null) throw new RuntimeException(
            'Attempt to use non-existent map.'
        );
        return $m;
    }

    /**
     * @return Model_Map_Abstract
     */
    final public function main_map(): Model_Map_Abstract {
        return $this->map_by_id($this->mapid());
    }

    /**
     * @param string $key Map ID to determine map, null to get main map
     * @return Model_Map_Abstract|null
     */
    final public function map_by_id($key): ?Model_Map_Abstract {
        if (!$key || !isset($this->set['gamedata']->maps[$key])) return null;
        else return $this->set['gamedata']->maps[$key];
    }

    /**
     * @param null $lid Location ID to determine map, null to get main map
     * @return string|null
     */
    final public function mapid($lid = null): ?string {
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
    final public function map_main() :Model_Map_Abstract {
        return $this->set['gamedata']->maps['main'];
    }

    /**
     * Returns all locations
     * @return int[]
     */
    final public function locations(): array {
        $ret = array();
        foreach ($this->set['gamedata']->maps as $map)
            /** @var $map Model_Map_Abstract */
            $ret = array_merge_recursive($map->get_locations(null, true),$ret);

        return $ret;
    }

    /**
     * Deletes all maps
     * @param string $map_cfg
     */
    final public function reset_maps($map_cfg): void {
        $this->config('game.config.map', $map_cfg);
        $this->set['gamedata']->maps = array();
        $this->set['gamedata']->maps['main'] = Model_Map_Abstract::factory($map_cfg);
        /** @noinspection PhpUndefinedMethodInspection */
        $this->set['gamedata']->maps['main']->auto_init();
    }

    /**
     * @return Model_Map_Abstract[]
     */
    final public function maps(): array {
        return array_values($this->set['gamedata']->maps);
    }

    /**
     * Returns a location by its ID
     *
     * @param $lid null|number Optional location id, when missing player location is assumed
     *
     * @return Model_Places_Abstract_Place|null
     * @throws Exception
     * @see Model_Gamelayer_Process::location()
     */
	final public function location($lid = NULL): ?Model_Places_Abstract_Place
    {
		$location = $lid ?? Globals::CurrentPlayerF()->location_class();

        if ($location < 0)
            /** @noinspection PhpUndefinedMethodInspection */
            return $this->set['gamedata']->maps['main']->get_by_fixed_id(-$location);
		else return $this->set['gamedata']->uin->get($location, 'Model_Places_Abstract_Place');
	}

    /**
     * Returns a location by its ID
     *
     * @param $lid null|number Optional location id, when missing player location is assumed
     *
     * @return Model_Places_Abstract_Place
     * @throws Exception
     * @see Model_Gamelayer_Process::location()
     */
    final public function locationF($lid = NULL): Model_Places_Abstract_Place
    {
        $location = $this->location($lid);
        if ($location === null) throw new LogicException(
            'Requested invalid location.'
        );
        return $location;
    }

    /**
     * @param $mapid
     * @param $sublocation
     * @param string|null $map_cfg
     * @return bool|number
     */
    final public function register_map($mapid, $sublocation, $map_cfg = null) {
        if (isset($this->set['gamedata']->maps[$mapid]))
            return false;

        $this->set['gamedata']->maps[$mapid] = Model_Map_Abstract::factory(
            $map_cfg ?? $this->config('game.config.map'), $sublocation);

        /** @noinspection PhpUndefinedMethodInspection */
        $this->set['gamedata']->maps[$mapid]->auto_init();
        /** @noinspection PhpUndefinedMethodInspection */
        return $this->set['gamedata']->maps[$mapid]->resolve_fixed_id(1);
    }

    final public function unregister_map($mapid): void {
        if (!($map = $this->map_by_id($mapid))) return;
        foreach ($map->get_locations() as $location) {
                $lobj = $this->location($location);
                if ($lobj) $lobj->grind();
                else $this->uin()->remove($location);
            }
        unset($this->set['gamedata']->maps[$mapid]);
    }

    /**
     * Returns the chat room name for this game
     * @return string
     */
    final public function chatroom(): string {
        return 'grg_private_' . $this->set['gameid'];
    }

    /**
     * Returns the player object associated with $pid; if $pid is not passed, the active player will be returned
     *
     * @param number|string|null $pid
     *
     * @return Model_Player|Interface_Plentity|null
     * @throws Exception
     */
    public function get_player($pid = NULL): ?Interface_Plentity {
    	if ($pid === NULL) {
            if (Globals::hasCurrentUser()) $pid = Globals::CurrentUserF()->uid();
            else return null;
        } elseif (!is_numeric($pid)) return $this->get_npc($pid);

    	if (!isset($this->set['gamedata']->players[$pid])) return null;
    	else return $this->set['gamedata']->uin->get($this->set['gamedata']->players[$pid], 'Model_Player');
    }

    /**
     * Returns the player name associated with $pid; if $pid is not passed, the active player will be used.
     * If $pid is invalid, ??? will be returned.
     * @param number|string|null $pid
     * @return string
     * @throws Exception
     */
    public function get_player_name($pid = NULL): string {
        $p = $this->get_player($pid);
        return $p ? $p->name() : '???';
    }

    /**
     * Returns a list of all available players
     *
     * @param boolean $limit_alive Set true if you want only living players to be returned (default: true)
     *
     * @return Model_Player[]
     * @throws Exception
     */
    public function players($limit_alive = true): array
    {
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
    public function get_npc($npcid): ?Interface_Plentity {
        if (!isset($this->set['gamedata']->npcs[$npcid])) return null;
        else return $this->set['gamedata']->uin->get($this->set['gamedata']->npcs[$npcid], 'Interface_Plentity');
    }

    /**
     * @param bool $limit_alive
     *
     * @return Interface_Plentity[]
     */
    public function npcs($limit_alive = true): array {
        $ret = Array();
        foreach ($this->set['gamedata']->npcs as $npc_id => $nid)
            if (!$this->get_npc($npc_id)) continue;
            elseif (!$limit_alive || $this->get_npc($npc_id)->get_status()->alive()) $ret[] = $this->get_npc($npc_id);

        return $ret;
    }

    /**
     * @param Interface_Plentity $npc
     * @param null|int|string $id
     * @return bool
     * @throws Exception
     */
    public function add_npc(Interface_Plentity $npc, $id = null): bool {
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

        foreach ($this->config('game.config.buffs.auto_npc') as $buff_cls)
            /** @var Model_Buffs_Abstract_Buff $buff_cls */
            new $buff_cls($npc);

            foreach ($this->get_initialized_events() as $ev)
            $ev->event_playerCreation($npc);

        return $id;
    }

    /**
     * @param bool $limit_alive
     *
     * @return Interface_Plentity[]
     * @throws Exception
     */
    public function playable_entities($limit_alive = true): array
    {
        return array_merge($this->players($limit_alive), $this->npcs($limit_alive));
    }

    /**
     * Adds a type value to the name doubling storage
     * @param string $id Reference ID
     * @param int $type Name ID
     */
    public function ndp_register($id, $type): void {
        if (!isset($this->set['gamedata']->ndp[$id]))
            $this->set['gamedata']->ndp[$id] = array($type);
        elseif (!in_array($type, $this->set['gamedata']->ndp[$id], true))
            $this->set['gamedata']->ndp[$id][] = $type;
    }

    /**
     * Checks, if an name ID is free
     * @param string $id Reference ID
     * @param int $type Name id
     * @return bool True, when name id is not yet registered under reference id
     */
    public function ndp_check($id, $type): bool {
        if (!isset($this->set['gamedata']->ndp[$id]))
            return true;
        else return !in_array($type, $this->set['gamedata']->ndp[$id], true);
    }

    /**
     * Removes all name ids registered under a given reference id
     * @param string $id Reference ID
     */
    public function ndp_purge($id): void {
        $this->set['gamedata']->ndp[$id] = array();
    }

    /**
     * @return Model_Events_Event[]
     */
    public function get_initialized_events(): array
    {
        if (!isset($this->set['gamedata']->active_events))
            $this->set['gamedata']->active_events = [];
        return $this->set['gamedata']->active_events;
    }

    /**
     * @param Model_Events_Event|string $e
     * @return Model_Events_Event|null
     */
    public function get_initialized_event($e): ?Model_Events_Event {
        if (Tool_System::instance_of($e,'Model_Events_Event'))
            $e = $e::get_key();
        $events = $this->get_initialized_events();
        if ($e && is_string($e) && isset($events[$e]))
            return $events[$e];
        else return null;
    }

    public function set_event_index(Model_Events_Event $event): bool {
        if ($event::is_current() && !$this->get_initialized_event($event)) {
            if ($event->is_active())
                $this->set['gamedata']->active_events[$event::get_key()] = $event;
            return $event->is_active();
        } else return false;
    }

    public function unset_event_index($event): bool {
        $event = $this->get_initialized_event($event);
        if (!$event) return false;
        elseif (!$event->is_active()) {
            unset($this->set['gamedata']->active_events[$event::get_key()]);
            return true;
        } else return false;
    }
}	