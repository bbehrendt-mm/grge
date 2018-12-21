<?php defined('SYSPATH') OR die('No direct access allowed.');

abstract class Model_Places_Abstract_Hideout extends Model_Places_Abstract_Place {

    protected static $outside = false;
    protected static $starts_built = false;
    protected static $alternative_default_hideout;

    protected static $base_deco_value = 0;
    private $deco_value = 0;

    //Base: 15% per day
    protected static $decay_rate = 0.15;

    //Exp: 8% per day
    protected static $decay_exp = 0.08;

    protected $extensions = Array();
    protected static $survival_find_available = false;

    public function uin($uin = NULL) {
        if ($uin === NULL) return parent::uin();
        else $t = parent::uin($uin);

        if (static::$starts_built) $this->setup_new_room($this->room(),[], static::$alternative_default_hideout ? [static::$alternative_default_hideout] : ['hideout']);

        $this->inventory->add(new Model_Items_Virtual_Location_Hideout());
        $this->defense = static::$base_defense;
        return $t;
    }

    public function setup_primary_rooms(): Model_Room
    {
        $room = parent::setup_primary_rooms();
        $room->upgrade('Versteck',false,['common_hideout']);
        $room->inventory()->add(new Model_Items_Virtual_Location_Room_Generic(
            'Verteidigen...', null, 'fighter'
        ));

        return $room;
    }

    public function setup_additional_rooms(): void
    {
        parent::setup_additional_rooms();
        $this->create_new_room(10,['inside']);
    }

    private function calculate_item_deco(): int
    {
        $a = 0;
        foreach ($this->inventory()->get() as $item)
            $a += $item->deco();
        return $a;
    }

    /**
     * @param null $add
     * @param bool $accum
     * @return number|number[]
     */
    public function deco($add = null, $accum = true) {
        if ($add === null) {
            $tmp = [static::$base_deco_value, ceil($this->decay * -40), $this->deco_value, $this->calculate_item_deco()];
            return $accum ? array_sum($tmp) : $tmp;
        }
        else return $this->deco_value += $add;
    }

    public function pretick(): void
    {
        //Decay
        if ($this->decay < 1) {
            $this->set_decay(static::$decay_rate * (1/288) * $this->patchup * (1 + floor($this->zombie_factory->accumulation())/5), false);
            $this->set_patchup(static::$decay_exp * (1/288), false);
        }

        //Check for zombie attack
        if ($this->get_defense() < 1) {
            parent::pretick();
            return;
        }

        //Accumulate zombies
        $this->zombie_factory->dry_spawn();

        $defense = $this->get_defense();

        /** @var Model_Items_Virtual_Epic_Fence $fence */
        if (($defense > 0) && floor($this->zombie_factory->accumulation()) > $defense && (!($fence = Tool_Scripts::first_item(Model_Items_Virtual_Epic_Fence::cls(), Struct_ScriptItemSource::onlyLocation())) || !$fence->get_status())) {
            if ($br = $this->find_rooms('bedroom','bedrwake')) {
                $br[0]->remove_content('bedrwake');
                foreach (Tool_Scripts::at_location($this->uin(), true, true) as $s_player)
                    if ($s_player->get_status()->retrieve('sleep_cozy')) {
                        $s_player->get_status()->retrieve('sleep_cozy')->unbuff();
                        new Model_Buffs_Exited($s_player, 4);
                        if (!Tool_Scripts::is_npc($s_player))
                            $s_player->log()->add('Du hörst den Alarmdraht klingen und springst aus dem Bett, um dich gegen Zombies zu verteidigen!');
                    }
            }

            $zombies = $this->zombie_factory->release();
            $num = array_reduce($zombies, function($c, $i) {
                /** @var $i Model_Combat_Zombies_Zombie */
                return $c + $i->count();
            }, 0);
            $battle = Tool_Scripts::combat([Tool_Scripts::at_location($this->obj_uin), $zombies], true, 10, $this, 'Die Zombies haben deine Verteidigung durchbrochen!');
            $this->zombie_factory()->accumulation($battle->count_group_members(2));

            foreach (Tool_Scripts::at_location($this->obj_uin, true, false) as $s_player)
                $s_player->achievements()->achieve(Model_Achievement::MA_BREAK_INS, $num);
        }

        foreach (Globals::CurrentGameF()->get_initialized_events() as $ev)
            $ev->event_locationTick($this);
    }

    public function tick($type = Interface_Tickable::IT_TYPE_PLAYER): bool
    {
        //Build chance array
        $chance = Array(Array('chance' => 1500, 'value' => 0),	//Nothing happens
            Array('chance' => 5, 'value' => 1),		//random small energy gain
            Array('chance' => 3, 'value' => 2),		//random medium energy gain
            Array('chance' => 1, 'value' => 3));	//random big energy gain

        //Act accordingly
        if (!Tool_Scripts::is_npc()) {
            $sleeping = Globals::CurrentPlayerF()->get_status()->retrieve('sleep_cozy');
            switch (Tool_Gambling::roulette($chance))
            {
                case 1:
                    if ($sleeping) Globals::CurrentPlayerActualF()->log()->add('Du hattest eben einen schönen Traum. Das hat dir etwas zusätzliche Energie verschafft.');
                    else Globals::CurrentPlayerActualF()->log()->add('Du hast soeben die Antwort auf eine philosophische Frage gefunden, die dich schon seit Jahren quält. Das hat dir etwas zusätzliche Energie verschafft.');
                    Globals::CurrentPlayerActualF()->get_status()->modify(Model_Status::MS_STAT_ENERGY, 5);
                    break;
                case 2:
                    if ($sleeping) Globals::CurrentPlayerActualF()->log()->add('Du hast die perfekte Ruheposition gefunden. Weil du jetzt so bequem liegst erhälst du einen Energieschub.');
                    else Globals::CurrentPlayerActualF()->log()->add('In deiner Hose findest du eine alte Kinokarte von einem Film, den du dir mit Freunden angesehen hast. Diese schöne Erinnerung verschafft dir einen Energieschub.');
                    Globals::CurrentPlayerActualF()->get_status()->modify(Model_Status::MS_STAT_ENERGY, 15);
                    break;
                case 3: Globals::CurrentPlayerActualF()->log()->add('Eine Sternschnuppe! So eine hast du schon ewig nicht mehr gesehen. Dieser wunderschöne Anblick gibt dir Hoffnung und einen gewaltigen Energieschub!');
                    Globals::CurrentPlayerActualF()->get_status()->modify(Model_Status::MS_STAT_ENERGY, 50);
                    break;
            }
        }


        return true;
    }



    public function enter($pid = null, $type = Interface_Tickable::IT_TYPE_PLAYER): bool
    {
        if (!$pid) $player = Globals::CurrentPlayerF();
        elseif ($type === Interface_Tickable::IT_TYPE_PLAYER) $player = Globals::CurrentGameF()->get_player($pid);
        else $player = Globals::CurrentGameF()->get_npc($pid);


        new Model_Buffs_Home($player);
        return parent::enter($pid, $type);
    }

    /**
     * @param null $pid
     * @param int  $type
     *
     * @return bool
     * @throws Exception
     */
    public function leave($pid = null, $type = Interface_Tickable::IT_TYPE_PLAYER
    ): bool {
        if (!$pid) $player = Globals::CurrentPlayerF();
        elseif ($type === Interface_Tickable::IT_TYPE_PLAYER) $player = Globals::CurrentGameF()->get_player($pid);
        else $player = Globals::CurrentGameF()->get_npc($pid);

        if (!parent::leave($pid, $type)) return false;
        if ($type === Interface_Tickable::IT_TYPE_PLAYER
            && ($dr = $this->find_rooms('', 'defimp'))
            && !$dr[0]->has_content('impaler')
        ) {
            $dr[0]->add_content('impaler');
            $player->log()->add(new Model_Log_Types_String( null, 'Auf dem Weg nach draußen hast du die Fallgrube wieder geschlossen und für einen erneuten Einsatz bereit gemacht.'));
        }

        if ($buff = $player->get_status()->retrieve('home')) $buff->unbuff();

        return true;
    }

    protected static $base_defense = 5;
    protected $defense;

    protected $decay = 1;
    protected $patchup = 1;

    public function get_defense($actual = false): float
    {
        return round($this->defense * ($actual ? 1 : (1 - $this->decay)));
    }

    public function get_decay(): int
    {
        return $this->decay;
    }

    public function get_patchup(): int
    {
        return $this->patchup;
    }


    public function set_decay($val, $absolute = true): void
    {
        if ($absolute)
            $this->decay = $val;
        else $this->decay += $val;

        $this->decay = min(1,max(0, $this->decay));
    }

    public function set_patchup($val, $absolute = true): void
    {
        if ($absolute)
            $this->patchup = $val;
        else $this->patchup += $val;

        $this->patchup = max(0, $this->patchup);
    }

    public function inc_defense($val): void
    {
        $this->defense += $val;
        if ($this->defense < 1) $this->defense = 1;
    }
}	