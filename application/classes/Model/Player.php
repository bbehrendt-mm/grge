<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Player extends Model_Cloudshard {

	const MP_STAT_HUNGER = 1;
	const MP_STAT_THIRST = 2;
	const MP_STAT_HEALTH = 3;
	const MP_STAT_SLEEPY = 4;
	const MP_STAT_ENERGY = 5;
	const MP_STAT_DRUNK  = 6;
	const MP_STAT_RADIATION = 7;
    const MP_STAT_ZOMBIFY = 8;
    const MP_STAT_FREEZE = 9;

	const MP_STATUS_COUNT = 9;
	const MP_THRESHOLD = 512;
	
	const MP_CHAR_DISTANCING = 512;
	const MP_CHAR_EVASIVENESS = 513;
	const MP_CHAR_ACCURACY = 514;
	const MP_CHAR_DAMAGE_RESISTANCE = 515;
	const MP_CHAR_BULKYNESS = 516;
    const MP_CHAR_DAMAGE_MULTIPLIER = 517;
    const MP_CHAR_LOCATION_SPAWNRATE = 518;

    const MP_SETTINGS_BATTLE_NOENERGY = 1;
    const MP_SETTINGS_BATTLE_NOSELFAMMO = 2;
    const MP_SETTINGS_BATTLE_DISTANCE_DAMAGE_SHIFT = 3;
    const MP_SETTINGS_BATTLE_NOTANKAMMO = 4;

	private $mode;
	private $job;
	private $level;

    private $companion = false;
    private $beacon = 0;
	
	private $user_id;
	private $name;
	private $status_bars;
    private $temp_registry;
	private $buffs;
	private $alive;
	private $location;
	private $inventory;
	private $cod = null;
    private $points = null;
    private $braincoins = 0;

    private $escape_target_location = null;

    private $messages = array();
	
	private $achievements;
	private $livetime = 0;

    private $timevote = 4;
    private $timelock = 0;
    private $last_action = 0;
    private $escape = 0;

    private $clock_repaired = false;

    private $april = false;
    private $got_ticket = false;

    private $battle_settings = array(
        Model_Player::MP_SETTINGS_BATTLE_NOENERGY => false,
        Model_Player::MP_SETTINGS_BATTLE_NOSELFAMMO => false,
        Model_Player::MP_SETTINGS_BATTLE_NOTANKAMMO => false,
        Model_Player::MP_SETTINGS_BATTLE_DISTANCE_DAMAGE_SHIFT => 2,
    );
	
	private $log;

    public function clock_state($new = null) {
        if ($new === null) return $this->clock_repaired;
        else $this->clock_repaired = $new;
    }

	public function __wakeup() {
		//Rebind global player variable
		global $user;
		
		if ($user && $user->uid() == $this->user_id) {
			global $player;
			$player = $this;
		}

        $this->temp_registry = array();

        //Reset above-threshold bars
        foreach ($this->status_bars as $key => &$value)
            if ($key >= static::MP_THRESHOLD) $value = 1;

		if ($this->alive())
			$this->cod = null;
	}

    public function set_escape_target($e = null) {
        $this->escape_target_location = $e;
    }

    public function get_escape_target() {
        return $this->escape_target_location;
    }

    public function register_temp($type) {
        if (isset($this->temp_registry[$type]))
            return false;
        return $this->temp_registry[$type] = true;
    }

    /**
     * @param bool $noenergy Avoid energy usage
     * @param bool $noselfammo Avoid throwing weapons
     * @param bool $notankammo Avoid tank weapons
     * @param int $ddshift DDSHIFT array
     * @param string[] $avoid Ammo types to avoid
     */
    public function set_battle_settings($noenergy, $noselfammo, $notankammo, $ddshift, $avoid) {
        $tmp = array(
            Model_Player::MP_SETTINGS_BATTLE_NOENERGY => ($this->job == 1080) ? true : $noenergy,
            Model_Player::MP_SETTINGS_BATTLE_NOSELFAMMO => $noselfammo,
            Model_Player::MP_SETTINGS_BATTLE_NOTANKAMMO => $notankammo,
            Model_Player::MP_SETTINGS_BATTLE_DISTANCE_DAMAGE_SHIFT => $ddshift
        );

        foreach ($avoid as $item) if (Tool_System::instance_of($item, 'Model_Items_Abstract_Ammo'))
            $tmp[$item] = true;

        $this->battle_settings = $tmp;
    }

    public function get_battle_settings() {
        if (!isset($this->battle_settings[Model_Player::MP_SETTINGS_BATTLE_NOTANKAMMO]))
            $this->battle_settings[Model_Player::MP_SETTINGS_BATTLE_NOTANKAMMO] = $this->battle_settings[Model_Player::MP_SETTINGS_BATTLE_NOSELFAMMO];
        return $this->battle_settings;
    }

    /**
     * Constructs a logical player that is linked to an user
     * @param int $user_id
     * @param string $name
     * @param int $mode
     * @param int $job
     * @param int $level
     */
	final public function __construct($user_id, $name, $mode, $job, $level) {
		global $user;
		
		//Set user ID and name
		$this->user_id = $user_id;
		$this->name = $name;
		
		//Set mode, job and level
		$this->mode = $mode;
		$this->job = $job;
		$this->level = $level;

        if ($this->job == 1080)
            $this->set_battle_settings(true, false, false, 2, array());
		
		//Init
		$this->status_bars = Array();
		$this->buffs = Array();
		$this->alive = true;
		$this->inventory = new Model_Inventory(null, true);
		$this->log = new Model_Log_Log();
		$this->achievements = new Model_Achievement();
		
		//Init all gameplay data
		$this->kickoff();
		
		//Bind global player variable
		if ($user->uid() == $this->user_id) {
			global $player;
			$player = $this;
		}
	}

    final public function april_fools($set = null) {
        if ($set === null)
            return $this->april;
        else return $this->april = $set;
    }

    final public function golden_ticket($set = null) {
        if ($set === null)
            return $this->got_ticket;
        else return $this->got_ticket = $set;
    }
	
	/**
	 * Initializes this player based on game mode and job level
	 */
	private function kickoff() {
		//Initial log message
		if ($this->mode == 2000) $this->log->add(new Model_Log_Types_Text('Das Gemetzel beginnt...', 'Tod allen Zombies!', 'Seit Wochen verschanzt du dich in deinem Versteck, doch jetzt platzt dir der Kragen. Das Leben ist scheiße, es gibt keinen Strom, tagsüber ist es heiß und nachts arschkalt, der Sand rieselt dir in jede Ritze. Und alles wegen diesen verfluchten Zombies! ES REICHT! Du schnappst dir deine Waffen und ziehst los, um es dem Gesindel mal ordentlich heimzuzahlen - und wenn es das letzte ist was du tust!'));
		elseif ($this->mode == 3000) $this->log->add(new Model_Log_Types_Text('Das Spiel beginnt...', 'Tagebuch eines Aufklärers', 'Nachdem du bereits eine Ewigkeit durch die Wüste gelatscht bist, hast du dieses heruntergekommene Versteck gefunden - Perfekt! Du entscheidest dich, es als Operationsbasis für deine Kartografietour zu verwenden und baust dein Funkequipment auf. Zeit, die Umgebung zu erkunden...'));
		elseif ($this->mode == 4000) $this->log->add(new Model_Log_Types_Text('Das Gemetzel beginnt...', 'Beginn einer Gladiatoren-Karriere', 'Ein Zettel ist soeben durch deinen Kamin geflogen... Du siehst ihn dir an und stellst fest, dass es sich um einen Werbeflyer für ein großes Zombieturnier im alten Kolosseum handelt. Das wär doch mal eine gelungene Abwechslung zum "im Versteck verrotten". Zunächst solltest du dich auf den Weg zum Kolosseum machen, um die Qualifikationsrunde zu absolvieren - du hast 24 Stunden Zeit dafür!'));
		else $this->log->add(new Model_Log_Types_Text('Das Spiel beginnt...', 'Deine Vorräte sind aufgebraucht!', 'Du öffnest die Augen und lässt deinen Blick durch dein karges Versteck schweifen. Deine Vorräte sind aufgebraucht, du kannst dich also nicht länger einfach verschanzen...'));
	}

    /**
     * @return Model_Inventory
     */
    final public function inventory() {
		return $this->inventory;
	}

    /**
     * Enables the player to escape a blockade
     */
    final public function enable_escape() {
        $this->escape = 2;
    }

    /**
     * Forbids the player to escape a blockade
     */
    final public function disable_escape() {
        $this->escape = 0;
    }

    /**
     * True, when the player is capable of escaping
     * @return bool
     */
    final public function can_escape() {
        return ($this->escape > 0);
    }
	
	/**
	 * Returns player name
	 * @return string
	 */
	final public function name() {
		return $this->name;
	}
	
	/**
	 * Returns player id
	 * @return int
	 */
	final public function id() {
		return $this->user_id;
	}
	
	/**
	 * Returns player location id or changes it
	 * @param int $newval Set if you want to change locations; the return value will be the new location
	 * @return int
	 */
	final public function location_class($newval = NULL) {
		if ($newval !== NULL) {
            $this->location = $newval;
            $this->escape = 0;
        }
		return $this->location;
	}
	
	/**
	 * Returns player location object
	 * @return Model_Places_Abstract_Place
	 */
	final public function location() {
        /**
         * @global $game Model_Game
         */
        global $game;
		return $game->location($this->location);
	}

    /**
     * Returns true if player is alive, or kills him if $set is false
     * @return bool
     */
	final public function alive() {
		return $this->alive;
	}

    final public function get_braincoins() {
        return $this->braincoins;
    }
	
	/**
	 * Kills player
	 */
	final public function kill() {
        /**
         * @global $game Model_Game
         */
		global $game;
		$this->log()->add(new Model_Log_Types_String('Du bist tot!','Du hast soeben deinen letzten Atemzug getan... Du bist auf die folgende schreckliche Art von dieser Welt gegangen: :cod!',[':cod' => [$this->cod]]));
		$this->calculate_static_achievements();
		$this->alive = false;

        $this->points = $game->points($this->user_id);
        $this->braincoins = Tool_Scripts::count_available_items('Model_Items_Braincoin', true, false, false, $this->id());

		if ($game->config('modules.multiplayer')) {
			$drop = array();
			foreach ($this->inventory->get() as $item)
				if ($dropping = $item->drop_dead()) {
					if (is_array($dropping)) foreach ($dropping as $d_drop) $drop[] = $d_drop;
					else $drop[] = $dropping;
				}
				
			$drop[] = new Model_Items_Body('[nt]' . $this->name, 'Dies ist alles, was von eurem Freund übrig geblieben ist... Naja, immerhin kann man noch eine Suppe draus kochen.');

            if ($this->stats_get(Model_Player::MP_STAT_ZOMBIFY) >= 50) {
                $ti = new Model_Inventory();

                foreach ($drop as $d)
                   $ti->add($d);

                $this->location()->log()->add(new Model_Log_Types_Item(Model_Log_Types_Item::MLTI_ZOMBIFY, array(), $this->id()));
                $game->register_ghul($this->location_class(), new Model_Battle_Ghul(mt_rand(50,100), $ti, $this->name(), $this->id(), $this->stats_get(Model_Player::MP_STAT_ZOMBIFY)));
            } else {
                foreach ($drop as $d)
                    $this->location()->inventory()->add($d);

                $this->location()->log()->add(new Model_Log_Types_Item(Model_Log_Types_Item::MLTI_DEATH, $drop, $this->id()));
            }


			if (count(Tool_Scripts::at_location()) == 0) $this->location()->vacate();
		}
	}
	
	/**
	 * Set cause of death
	 * @param String $new_cod New cause of death
	 */
	public function set_cod($new_cod) {
		if ($this->alive) $this->cod = $new_cod;
	}
	
	public function get_cod() {
		return $this->cod;
	}
	
	/**
	 * Changes players stats and rebuilds buffers afterwards
	 * @param Array $args Supposed to be in this format: [stat1, change1, stat2, change2, ...]
	 * @throws Exception When $args is wrong format
	 */
	final public function stats_modify($args) {
		if (!is_array($args)) $args = func_get_args();
		
		//Make sure the input format is correct
		if (count($args) % 2) throw new Exception('Invalid input format for stat modificator!');
		
		//Run over each input pair
		$i = 0;
		while ($i < count($args)) {
			//Check if value is set already and calculate change
			if (!isset($this->status_bars[$args[$i]]))
                $this->status_bars[$args[$i]] = ($args[$i] >= static::MP_THRESHOLD) ? 1 : 0;

			$this->status_bars[$args[$i]] += $args[$i+1];
			
			//Enforce bounds (0/100)
			$this->status_bars[$args[$i]] = min(max($this->status_bars[$args[$i]],0),100);
			
			//Jump to next pair
			$i += 2;
		}
		
		$this->rebuild_buffs();
	}
	
	/**
	 * Sets players stats (ignoring their previous values) and rebuilds buffers afterwards
	 * @param Array $args Supposed to be in this format: [stat1, newval1, stat2, newval2, ...]
	 * @throws Exception When $args is wrong format
	 */
	final public function stats_set($args) {
		if (!is_array($args)) $args = func_get_args();
	
		//Make sure the input format is correct
		if (count($args) % 2) throw new Exception('Invalid input format for stat modificator!');
	
		//Run over each input pair
		$i = 0;
		while ($i < count($args)) {
			//Set new value
			$this->status_bars[$args[$i]] = $args[$i+1];
				
			//Enforce bounds (0/100)
			$this->status_bars[$args[$i]] = min(max($this->status_bars[$args[$i]],0),100);
				
			//Jump to next pair
			$i += 2;
		}
		
		$this->rebuild_buffs();
	}
	
	/**
	 * Returns a specific status value; if this value has not been set, returns 0
	 * @param int $stat
	 * @return int
	 */
	final public function stats_get($stat) {
		if (!isset($this->status_bars[$stat])) return ($stat >= static::MP_THRESHOLD) ? 1 : 0;
        elseif ($stat >= static::MP_THRESHOLD)
            return max(0,1 + $this->stats_buffs($stat));
        else return $this->status_bars[$stat];
	}
	
	/**
	 * Returns all active status bars
	 * @return number[]
	 */
	public function active_stats() {
		return array_keys($this->status_bars);
	}
	
	/**
	 * Rebuilds all buffs
	 */
	private function rebuild_buffs() {
        /**
         * @var $buff Model_Buffs_Abstract_Buff
         */
        foreach ($this->buffs as $buff)
			$buff->rebuild();
	}

    public function rebuild() {
        $this->rebuild_buffs();
    }
	
	/**
	 * Return status effects one one specific stat caused by buffs
	 * @param int $stat
	 * @return int
	 */
	final public function stats_buffs($stat) {
        /**
         * @var $buff Model_Buffs_Abstract_Buff
         */
		$raise_acc = $drop_acc = 0;
		$raise_prc = $drop_prc = 1;
		
		foreach ($this->buffs as $buff) {
			$raise_acc += $buff->effect($stat, Model_Buffs_Abstract_Buff::MB_RAISE_ACC);
			$raise_prc += $buff->effect($stat, Model_Buffs_Abstract_Buff::MB_RAISE_PRC);
			$drop_acc += $buff->effect($stat, Model_Buffs_Abstract_Buff::MB_DROP_ACC);
			$drop_prc += $buff->effect($stat, Model_Buffs_Abstract_Buff::MB_DROP_PRC);
		}
		
		return max(0,($raise_acc * max($raise_prc,0))) - max(0,($drop_acc * max($drop_prc,0)));
	}
	
	/**
	 * Adds a new buff
	 * @param Model_Buffs_Abstract_Buff $buff
	 */
	final public function buff_add($buff) {
        /**
         * @var $p Model_Buffs_Abstract_Buff|null
         */

        //Get existing buff
        $p = isset($this->buffs[$buff->bid()]) ? $this->buffs[$buff->bid()] : null;

		if ($p) {
            if ($p->get_dominance() > $buff->get_dominance()) return;
            elseif ($p->get_dominance() < $buff->get_dominance()) $this->buffs[$buff->bid()] = $buff;
            else $p->merge($buff);
        } else $this->buffs[$buff->bid()] = $buff;
	}
	
	/**
	 * Removes a buff
	 * @param Model_Buffs_Abstract_Buff|string $obj
	 */
	final public function buff_remove($obj) {
		if (is_object($obj)) {
            $obj->remove();
            unset($this->buffs[$obj->bid()]);
        }
		else
        {
            $tmp = explode('/', $obj);
            $obj = $tmp[0];

            if (isset($this->buffs[$obj]) && isset($tmp[1]) && $this->buff_retr($obj)->abid() != $tmp[1])
                return;

            if (isset($this->buffs[$obj]))
                /** @noinspection PhpUndefinedMethodInspection */
                $this->buffs[$obj]->remove();
            unset($this->buffs[$obj]);
        }
	}
	
	/**
	 * If a buff specified by $id is set, this function retrieves it, otherwise NULL is returned.
	 * @param string $id
	 * @return Model_Buffs_Abstract_Buff
	 */
	final public function buff_retr($id) {
        $tmp = explode('/', $id);
        $id = $tmp[0];

        /** @noinspection PhpUndefinedMethodInspection */
        if (isset($this->buffs[$id]) && isset($tmp[1]) && $this->buffs[$id]->abid() != $tmp[1])
            return NULL;

        if (isset($this->buffs[$id])) return $this->buffs[$id];
		else return NULL;
	}
	
	/**
	 * Returns all active buffs
	 * @return Model_Buffs_Abstract_Buff[]
	 */
	final public function buff_get() {
		return $this->buffs;
	}

    /**
     * Returns player log
     * @return Model_Log_Log
     */
	final public function log() {
		return $this->log;
	}
	
	/**
	 * Tick actions
	 */
	public function tick() {
        /**
         * @var $buff Model_Buffs_Abstract_Buff
         */
        if (!$this->alive()) return;
		
		$tmp = Array();
		$this->livetime++;
        if ($this->escape > 0)
            $this->escape--;

		foreach (array_keys($this->status_bars) as $stat) {
			$tmp[] = $stat;
			$tmp[] = $this->stats_buffs($stat);
		}
		
		$this->set_cod("Multiorganversagen");
		$this->stats_modify($tmp);
		$this->set_cod(null);
		
		foreach ($this->buffs as $buff) $buff->tick();

        //Death fix
        if ($this->alive && !$this->buff_retr('heartbeat'))
            $this->kill();
        else {
            //Tick achievements

            //Gametime achievements
            if ($this->livetime && !($this->livetime % 288))    //day
                $this->achievements->achieve(Model_Achievement::MA_GAMETIME);
            if ($this->livetime && !($this->livetime % 2016))    //week
                $this->achievements->achieve(Model_Achievement::MA_GAMEWEEK);
            if ($this->livetime && !($this->livetime % 8064))    //month
                $this->achievements->achieve(Model_Achievement::MA_GAMEMONTH);
        }
	}
	
	/**
	 * Refreshes all char values
	 */
	public function refresh_char_values() {
		$tmp = Array();
        foreach (array_keys($this->status_bars) as $stat) if ($stat >= static::MP_THRESHOLD) {
			$tmp[] = $stat;
			$tmp[] = $this->stats_buffs($stat);
		}
		
		$this->stats_modify($tmp);
	}
	
	/**
	 * Create a combatant object to use im battles
	 * @return Model_Battle_Player
	 */
	public function create_contestant() {
		if ($this->job == 1040 && $this->level >= 5 && mt_rand(0,15) == 2)
			return new Model_Battle_Saint($this->user_id);
		return new Model_Battle_Player($this->user_id);
	}
	
	/**
	 * Get achievement object
	 * @return Model_Achievement
	 */
	public function achievements() {
		return $this->achievements;
	}
	
	/**
	 * Returns player lifetime
	 * @return number
	 */
	public function get_lifetime() {
		return $this->livetime;	
	}
	
	public function calculate_static_achievements() {
		//Other endtime achievements
		$this->achievements->achieve_force(Model_Achievement::MA_SOME_COMPANY, 2 * count(Tool_Scripts::get_home_items('Model_Items_Generic_Bobblehead')) + count(Tool_Scripts::get_home_items('Model_Items_Generic_Teddy')) + count(Tool_Scripts::get_home_items('Model_Items_Body')));
		$this->achievements->achieve_force(Model_Achievement::MA_PRINCESS, count(Tool_Scripts::get_home_items('Model_Items_Generic_Bed')));
		$this->achievements->achieve_force(Model_Achievement::MA_ITEM_COUNT, count(Tool_Scripts::get_home_items('Model_Items_Abstract_Item')));
	
		$this->achievements->compile();
	}
	
	/**
	 * Returns player job details
	 * @param null|boolean|number $compare_job Null: Return job id; false: Compare level only; any number: Compare job
	 * @param null|number $compare_level Null: Return level ($compare_job needs to be false!); any number: Compare job and level
	 * @param boolean $exact True to check if level matches exactly; false to check if level is equal or greater than given level
	 * @return int|boolean
	 */
	public function job($compare_job = null, $compare_level = null, $exact = true) {
        if ($compare_job && $compare_level === null)
            return ($this->job == $compare_job);

        if ($compare_job === null) return $this->job;
		elseif ($compare_job === false && $compare_level === null) return $this->level;
		elseif ($compare_job === false) return ($exact) ? ($compare_level == $this->level) : ($compare_level <= $this->level);
		else return ($this->job == $compare_job && (($exact) ? ($compare_level == $this->level) : ($compare_level <= $this->level)));
	}

    public function get_points() {
        return $this->points;
    }

    /**
     * Creates game ranking entry one the player has been killed
     * @param $season
     * @param $gameid
     * @param bool $rank
     * @param int $start
     * @param int $end
     */
    public function expire($season, $gameid, $rank = false, $start = 0, $end = 0) {
        /**
         * @global $game Model_Game
         */
        global $game;

        if ($this->points === null)
            $this->points = $game->points($this->user_id);
		//Create ranking entry if game is rankable and player has more than zero points
		try
		{
			if ($rank && $this->points > 0)
			{				
				$this->calculate_static_achievements();
				DB::insert('ranking', array('season', 'gameid', 'uid', 'points', 'ticks', 'job', 'board', 'flow', 'start', 'end'))->values(array($season, $gameid, $this->user_id, $this->points, $this->livetime, $this->job, $this->mode, $game->timeflow(), $start, $end))->execute();
				$this->achievements->award($this->user_id, $gameid, $season);
			}	
		}
		catch (exception $e)
		{
			Log::instance()->add(Log::ERROR, 'Ranking error :' . $e->getMessage());
			Log::instance()->write();
		}
	}
	
	public function user_id() {
		return $this->user_id;
	}


    /**
     * Returns the companion state, or sets it when newval is given
     * @param null $newval
     * @return bool
     */
    public function companion($newval = null) {
        if ($newval === null) return $this->companion;
        else return $this->companion = $newval;
    }


    /**
     * Activates the chat beacon for a given amount of time, or returns the beacon status
     * @param null $minutes Null to return beacon state; negative number to reset beacon state; positive number to activate beacon
     * @return bool|number
     */
    public function chat_beacon($minutes = null) {
        if ($minutes === null) return ($this->beacon >= time());
        elseif ($minutes < 0) return $this->beacon = 0;
        else return $this->beacon = time() + $minutes * 60;
    }

    /**
     * Receive a message
     * @param int $uid User id
     * @param string $message
     * @param string $title
     */
    public function add_message($uid, $message, $title) {
        $id = time() . mt_rand(0,99);
        $this->messages[$id] = array('uid' => $uid, 'message' => $message, 'title' => $title, 'timestamp' => time(), 'read' => false, 'mid' => $id);
        while (count($this->messages) > 50) {
            $d = array_keys($this->messages);
            $this->delete_message($d[0]);
        }
    }

    /**
     * Delete a message
     * @param $id
     */
    public function delete_message($id) {

        unset($this->messages[$id]);
    }

    /**
     * Mark message as read
     * @param $id
     */
    public function read_message($id) {
        if (isset($this->messages[$id])) $this->messages[$id]['read'] = true;
    }

    /**
     * Returns all messages
     * @param bool $reverse
     * @param bool $filter_read
     * @return array
     */
    public function get_messages($reverse = true, $filter_read = false) {
        if ($filter_read) {
            $ret = array();
            foreach ($this->messages as $msg)
                if (!$msg['read'])
                    $ret[] = $msg;
        } else $ret = $this->messages;

        return $reverse ? array_reverse($ret) : $ret;
    }

    /**
     * Time vote controll for Multiplayer games
     * @param null|bool|int $vote Number to set new vote, true to return locktime, null to return current vote
     * @param null|int $lock_duration Time to lock for new votes
     * @return bool|int
     */
    public function vote_time($vote = null, $lock_duration = null) {
        if ($vote === null)
            return $this->timevote;

        if ($vote === true)
            return $this->timelock;

        if ($this->timelock > time() || $lock_duration === null)
            return false;

        $this->timevote = $vote;
        $this->timelock = time() + $lock_duration;
        return true;
    }

    public function last_action($set = false) {
        if ($set)
            return $this->last_action = time();
        else return $this->last_action;
    }
}
