<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Player extends Model_Cloudshard implements Interface_Plentity {

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
    private $status;
    private $temp_registry;
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

    private $april = false;
    private $got_ticket = false;

    private $battle_settings = array(
        Model_Player::MP_SETTINGS_BATTLE_NOENERGY => false,
        Model_Player::MP_SETTINGS_BATTLE_NOSELFAMMO => false,
        Model_Player::MP_SETTINGS_BATTLE_NOTANKAMMO => false,
        Model_Player::MP_SETTINGS_BATTLE_DISTANCE_DAMAGE_SHIFT => 2,
    );
	
	private $log;

	public function __wakeup() {
		//Rebind global player variable
		/** @global Model_Euser $user */
		global $user;
		
		if ($user && $user->uid() == $this->user_id) {
			global $player;
			$player = $this;
		}

        $this->temp_registry = array();

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
		/** @global Model_Euser $user */
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
		$this->alive = true;
		$this->inventory = new Model_Inventory(null, true);
		$this->log = new Model_Log_Log();
		$this->achievements = new Model_Achievement();
        $this->status = new Model_Status();
		
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

        if (!$game->location($this->location))
            $this->location_class($game->map_main()->resolve_fixed_id(1));

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

            if ($this->get_status()->get(Model_Status::MS_STAT_ZOMBIFY) >= 50) {
                $ti = new Model_Inventory();

                foreach ($drop as $d)
                   $ti->add($d);

                $this->location()->log()->add(new Model_Log_Types_Item(Model_Log_Types_Item::MLTI_ZOMBIFY, array(), $this->id()));
				$game->register_ghul($this->location_class(), Model_Combat_Zombies_Ghul::factory()->zombiefied_player_id($this->id())->name($this->name())->register_inventory($this->inventory())->strength(Model_Status::MS_STAT_ZOMBIFY, 100, 1));
            } else {
                foreach ($drop as $d)
                    $this->location()->inventory()->add($d);

                $this->location()->log()->add(new Model_Log_Types_Item(Model_Log_Types_Item::MLTI_DEATH, $drop, $this->id()));
            }


			if (count(Tool_Scripts::at_location($this->location_class(), true, true)) == 0) $this->location()->vacate();
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

	final public function is_actual_player() {
		return true;
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

		$this->livetime++;
        if ($this->escape > 0)
            $this->escape--;
		
		$this->set_cod("Multiorganversagen");
		$this->get_status()->tick();
		$this->set_cod(null);

        //Death fix
        if ($this->alive && !$this->get_status()->retrieve('heartbeat'))
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
     * @return Model_Status
     */
    public function get_status() {
        return $this->status;
    }

	public function create_combatant() {
		if ($this->job == 1040 && $this->level >= 5 && mt_rand(0,15) == 2)
			return Model_Combat_Players_Saint::create_linked_actor($this);
		return Model_Combat_Players_Player::create_linked_actor($this);
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
	 * @param null|number $filter
	 * @param bool $primary
	 * @return Model_Items_Abstract_Equipable[]
	 */
	public function get_equipment($filter = null, $primary = false) {
		$items = $this->inventory()->get('Model_Items_Abstract_Equipable');
		return array_values(array_filter($items, function($i) use ($filter, $primary) {
			/** @var Model_Items_Abstract_Equipable $i */
			return $i->is_equipped() && ($filter === null || $i->get_equipment_type() == $filter) && (!$primary || $i->is_equipped_primary());
		}));
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
        /** @global Model_Game $game */
        global $game;

        if ($vote === null)
            return $this->timevote;

        if ($vote === true)
            return $this->timelock;

        if ($this->timelock > time() || $lock_duration === null)
            return false;

        $this->timevote = $vote;

        if ($game->duration())
            $this->timelock = time() + $lock_duration;

        return true;
    }

    public function last_action($set = false) {
        if ($set)
            return $this->last_action = time();
        else return $this->last_action;
    }

    public function ai() {/* Player Object has no AI */}

    public function can($type) {
        return true;
    }

    public function type() {
        return static::IC_NPC_NONPC;
    }
}
