<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Player extends Model_NPC_Nano {

    protected static $entity_type = Interface_Plentity::IC_NPC_NONPC;

    protected static $escort_functions = [
        Interface_Plentity::IC_ALLOW_ITEM_PICKUP, Interface_Plentity::IC_ALLOW_ITEMS_SIDEUSE,
        Interface_Plentity::IC_ALLOW_SHOW_INVENTORY, Interface_Plentity::IC_ALLOW_MOVE
    ];

	private $mode;
	private $job;
	private $level;

    private $points;
    private $braincoins = 0;
    private $braincoin_factor = 1.0;
	
	private $achievements;

    private $timevote = 4;
    private $timelock = 0;
    private $last_action = 0;

    private $april = false;
    private $got_ticket = false;

    private $ai_str = '0000';

    protected $battle_player_stats = [5,5,5,5];

    protected $postbox;
	
	private $log;

	public function __wakeup() {
		//Rebind global player variable
		if (Globals::hasCurrentUser() && Globals::CurrentUserF()->uid() === $this->id)
		    Globals::setPrimaryPlayer($this);
	}

    public function ai($s = null) {
        if ($s === null) return $this->ai_str;

        if (strlen($s) !== 4) return false;
        for ($i = 0; $i < 4; $i++)
            if (!in_array($s[$i], ['+', '-', '0'], true))
                $s[$i] = '0';

        return $this->ai_str = $s;
    }

    public function set_braincoin_factor(float $v): void {
        $this->braincoin_factor = $v;
    }

    public function get_braincoin_factor(): float {
	    return $this->braincoin_factor;
    }

    /**
     * Constructs a logical player that is linked to an user
     *
     * @param int    $user_id
     * @param string $name
     * @param int    $mode
     * @param int    $job
     * @param int    $level
     *
     * @throws Exception
     */
	final public function __construct($user_id, $name, $mode, $job, $level) {
        parent::__construct($name);

		//Set user ID and name
		$this->id = $user_id;
		
		//Set mode, job and level
		$this->mode = $mode;
		$this->job = $job;
		$this->level = $level;
		
		//Init
		$this->log = new Model_Log_Log();
		$this->achievements = new Model_Achievement();
        $this->postbox = new Model_Postbox();
		
		//Init all gameplay data
		$this->kickoff();
		
		//Bind global player variable
		if (Globals::hasCurrentUser() && Globals::CurrentUserF()->uid() === $this->id)
		    Globals::setCurrentPlayer($this);
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
	private function kickoff(): void
    {
		//Initial log message
		if ($this->mode === 2000) $this->log->add(    new Model_Log_Types_String('Das Gemetzel beginnt...', 'Seit Wochen verschanzt du dich in deinem Versteck, doch jetzt platzt dir der Kragen. Das Leben ist scheiße, es gibt keinen Strom, tagsüber ist es heiß und nachts arschkalt, der Sand rieselt dir in jede Ritze. Und alles wegen diesen verfluchten Zombies! ES REICHT! Du schnappst dir deine Waffen und ziehst los, um es dem Gesindel mal ordentlich heimzuzahlen - und wenn es das letzte ist was du tust!'));
		elseif ($this->mode === 3000) $this->log->add(new Model_Log_Types_String('Das Spiel beginnt...', 'Nachdem du bereits eine Ewigkeit durch die Wüste gelatscht bist, hast du dieses heruntergekommene Versteck gefunden - Perfekt! Du entscheidest dich, es als Operationsbasis für deine Kartografietour zu verwenden und baust dein Funkequipment auf. Zeit, die Umgebung zu erkunden...'));
		elseif ($this->mode === 4000) $this->log->add(new Model_Log_Types_String('Das Gemetzel beginnt...', 'Ein Zettel ist soeben durch deinen Kamin geflogen... Du siehst ihn dir an und stellst fest, dass es sich um einen Werbeflyer für ein großes Zombieturnier im alten Kolosseum handelt. Das wär doch mal eine gelungene Abwechslung zum "im Versteck verrotten". Zunächst solltest du dich auf den Weg zum Kolosseum machen, um die Qualifikationsrunde zu absolvieren - du hast 24 Stunden Zeit dafür!'));
		else $this->log->add(                        new Model_Log_Types_String('Das Spiel beginnt...', 'Du öffnest die Augen und lässt deinen Blick durch dein karges Versteck schweifen. Deine Vorräte sind aufgebraucht, du kannst dich also nicht länger einfach verschanzen...'));
	}

    final public function get_braincoins(bool $include_factor = true): int
    {
        return $include_factor ? floor($this->braincoins * $this->braincoin_factor) : $this->braincoins;
    }


    protected function generate_dead_body(): ?Model_Items_Abstract_Item
    {
        return new Model_Items_Body('[nt]' . $this->name, 'Dies ist alles, was von eurem Freund übrig geblieben ist... Naja, immerhin kann man noch eine Suppe draus kochen.');
    }

    protected function generate_zombified_body() {
        return Model_Combat_Zombies_Ghul::factory()->zombiefied_player_id($this->id)->name($this->name())->register_inventory($this->inventory())->strength(Model_Status::MS_STAT_ZOMBIFY, 100, 1);
    }

	/**
	 * Kills player
	 */
	public function kill() {
        // Chat room
        if (Globals::CurrentGameF()->config('modules.multiplayer'))
            Controller_Chat::revoke_registration($this->id(),Globals::CurrentGameF()->id());

		$this->log()->add(new Model_Log_Types_String('Du bist tot!','Du hast soeben deinen letzten Atemzug getan... Du bist auf die folgende schreckliche Art von dieser Welt gegangen: :cod!',[':cod' => [$this->get_status()->get_cause_of_death()]]));
		$this->calculate_static_achievements();

        $this->points = Globals::CurrentGameF()->points($this->id);
        $this->braincoins = Tool_Scripts::count_items(Model_Items_Braincoin::cls(), Struct_ScriptItemSource::onlyPlayer()->use_perspective($this));
        Globals::CurrentGameF()->register_death($this->id);

        parent::kill();
	}

    /**
     * Returns player log
     * @return Model_Log_Log
     */
	final public function log(): \Model_Log_Log
    {
		return $this->log;
	}
	
	/**
	 * Tick actions
	 */
	public function tick() {
        if (!$this->get_status()->alive()) return;

        parent::tick();

        if ($this->escape > 0)
            $this->escape--;

        //Death fix
        if ($this->get_status()->alive() && !$this->get_status()->retrieve('heartbeat'))
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

	public function create_combatant() {
		if ($this->job === 1040 && $this->level >= 5 && random_int(0,15) === 2)
			return Model_Combat_Players_Saint::create_linked_actor($this);
		return Model_Combat_Players_Player::create_linked_actor($this);
	}
	
	/**
	 * Get achievement object
	 * @return Model_Achievement
	 */
	public function achievements(): \Model_Achievement
    {
		return $this->achievements;
	}

    public function battle_stats($new = null): array
    {
        if ($new !== null)
            for ($i = 0; $i < 4; $i++)
                if ($new[$i] !== null)
                    $this->battle_player_stats[$i] = $new[$i];
        return $this->battle_player_stats;
    }

	/**
	 * Returns player lifetime
	 * @return number
	 */
	public function get_lifetime() {
		return $this->livetime;	
	}
	
	public function calculate_static_achievements(): void
    {
        // Get bodies with the achievement flag
		$bodies = array_filter(Tool_Scripts::get_home_items(Model_Items_Body::cls()), function(Model_Items_Body $item) {
            return $item->get_enable_achievement();
        });

        //Other end-time achievements
		$this->achievements->achieve_force(Model_Achievement::MA_SOME_COMPANY, 2 * count(Tool_Scripts::get_home_items(Model_Items_Generic_Bobblehead::cls())) + count(Tool_Scripts::get_home_items(Model_Items_Generic_Teddy::cls())) + count($bodies));
		$this->achievements->achieve_force(Model_Achievement::MA_PRINCESS, count(Tool_Scripts::get_home_items(Model_Items_Generic_Bed::cls())));
		$this->achievements->achieve_force(Model_Achievement::MA_ITEM_COUNT, count(Tool_Scripts::get_home_items(Model_Items_Abstract_Item::cls())));
	
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
            return ($this->job === $compare_job);

        if ($compare_job === null) return $this->job;
		elseif ($compare_job === false && $compare_level === null) return $this->level;
		elseif ($compare_job === false) return $exact ? ($compare_level === $this->level) : ($compare_level <= $this->level);
		else return ($this->job === $compare_job && ($exact ? ($compare_level === $this->level) : ($compare_level <= $this->level)));
	}

    public function job_child(): bool {
        return $this->job(1080) || $this->job(1081);
    }

    public function get_points() {
        return $this->points;
    }

    /**
     * Creates game ranking entry one the player has been killed
     * @param      $season
     * @param      $gameid
     * @param bool $rank
     * @param int  $start
     * @param int  $end
     * @throws Exception
*/
    public function expire($season, $gameid, $rank = false, $start = 0, $end = 0): void
    {
        $this->get_status()->alive(false);

        if ($this->points === null)
            $this->points = Globals::CurrentGameF()->points($this->id);
		//Create ranking entry if game is rankable and player has more than zero points
		try
		{
			if ($rank && $this->points > 0)
			{				
				$this->calculate_static_achievements();
				DB::insert('ranking', array('season', 'gameid', 'uid', 'points', 'ticks', 'job', 'board', 'flow', 'start', 'end'))->values(array($season, $gameid, $this->id, $this->points, $this->livetime, $this->job, $this->mode, Globals::CurrentGameF()->timeflow(), $start, $end))->execute();
				$this->achievements->award($this->id, $gameid, $season);
			}	
		}
		catch (exception $e)
		{
			Log::instance()->add(Log::ERROR, 'Ranking error :' . $e->getMessage());
			Log::instance()->write();
		}
	}

    /**
     * @param null|number $filter
     * @param bool        $primary
     *
     * @return Model_Items_Abstract_Equipable[]
     * @throws Exception
     */
	public function get_equipment($filter = null, $primary = false): array
    {
		$items = $this->inventory()->get(Model_Items_Abstract_Equipable::cls());
		return array_values(array_filter($items, function($i) use ($filter, $primary) {
			/** @var Model_Items_Abstract_Equipable $i */
			return $i->is_equipped() && ($filter === null || $i->get_equipment_type() === $filter) && (!$primary || $i->is_equipped_primary());
		}));
	}

    /**
     * @return Model_Postbox
     */
    public function get_postbox(): \Model_Postbox
    {
        return $this->postbox;
    }

    /**
     * Time vote controll for Multiplayer games
     * @param null|bool|int $vote          Number to set new vote, true to return locktime, null to return current vote
     * @param null|int      $lock_duration Time to lock for new votes
     * @return bool|int
     * @throws Exception
*/
    public function vote_time($vote = null, $lock_duration = null) {
        if ($vote === null)
            return $this->timevote;

        if ($vote === true)
            return $this->timelock;

        if ($lock_duration === null || $this->timelock > time())
            return false;

        $this->timevote = $vote;

        if (Globals::CurrentGameF()->duration())
            $this->timelock = time() + $lock_duration;

        return true;
    }

    public function last_action($set = false): int {
        if ($set)
            return $this->last_action = time();
        else return $this->last_action;
    }

    public function can($type) {
        return true;
    }

    public function entity_species() {
        return 'Mensch';
    }

    public function entity_profession() {
        return Tool_Modes::get_job_by_id($this->job);
    }

    public function entity_description() {
        return 'Dieser Charakter wird von einem anderen Spieler kontrolliert.';
    }

    public function icon() {
        return $this->job_child() ? 'child.gif' : 'adult.gif';
    }
}
