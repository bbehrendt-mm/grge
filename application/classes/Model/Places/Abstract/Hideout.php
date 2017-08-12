<?php defined('SYSPATH') OR die('No direct access allowed.');

abstract class Model_Places_Abstract_Hideout extends Model_Places_Abstract_Place {

    protected static $outside = false;

    protected static $base_deco_value = 0;
    private $deco_value = 0;

    //Base: 15% per day
    protected static $decay_rate = 0.15;

    //Exp: 8% per day
    protected static $decay_exp = 0.08;

    protected $extensions = Array();
    protected $survival_find = true;
    protected $upgradable = true;

    public function uin($uin = NULL) {
        if ($uin === NULL) return parent::uin();
        else $t = parent::uin($uin);
        //ToDo: Rooms

        $this->inventory->add(new Model_Items_Virtual_Location_Hideout(!$this->room()->has_content('cursed_hideout')));
        return $t;
    }

    public function setup_primary_rooms() {
        $room = parent::setup_primary_rooms();
        $room->upgrade('Versteck',false,['common_hideout']);
        $room->inventory()->add(new Model_Items_Virtual_Location_Room_Generic("Verteidigen...", null, "fighter"));

        return $room;
    }

    public function setup_additional_rooms() {
        parent::setup_additional_rooms();
        $this->create_new_room(10,['inside']);
    }

    private function calculate_item_deco() {
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

    public function pretick() {
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

        /** @var Model_Items_Virtual_Epic_Fence $fence */
        //ToDO Fix bedrwake for use with multiple rooms
        if (($this->get_defense() > 0) && floor($this->zombie_factory->accumulation()) > $this->get_defense() && (!($fence = Tool_Scripts::first_available_item('Model_Items_Virtual_Epic_Fence', false)) || !$fence->get_status())) {
            if ($this->room()->has_content("bedrwake")) {
                $this->room()->remove_content("bedrwake");
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
            $battle = Tool_Scripts::combat([Tool_Scripts::at_location($this->uin), $zombies], true, 10, $this, 'Die Zombies haben deine Verteidigung durchbrochen!');
            $this->zombie_factory()->accumulation($battle->count_group_members(2));

            foreach (Tool_Scripts::at_location($this->uin, true, false) as $s_player)
                $s_player->achievements()->achieve(Model_Achievement::MA_BREAK_INS, $num);
        }

        foreach (Globals::CurrentGame()->get_initialized_events() as $ev)
            $ev->event_locationTick($this);
    }

    public function tick($type = Interface_Tickable::IT_TYPE_PLAYER) {
        //Build chance array
        $chance = Array(Array('chance' => 1500, 'value' => 0),	//Nothing happens
            Array('chance' => 5, 'value' => 1),		//random small energy gain
            Array('chance' => 3, 'value' => 2),		//random medium energy gain
            Array('chance' => 1, 'value' => 3));	//random big energy gain

        //Act accordingly
        if (!Tool_Scripts::is_npc()) {
            $sleeping = Globals::CurrentPlayer()->get_status()->retrieve('sleep_cozy');
            switch (Tool_Gambling::roulette($chance))
            {
                case 1:
                    if ($sleeping) Globals::CurrentPlayerActual()->log()->add('Du hattest eben einen schönen Traum. Das hat dir etwas zusätzliche Energie verschafft.');
                    else Globals::CurrentPlayerActual()->log()->add('Du hast soeben die Antwort auf eine philosophische Frage gefunden, die dich schon seit Jahren quält. Das hat dir etwas zusätzliche Energie verschafft.');
                    Globals::CurrentPlayerActual()->get_status()->modify(Model_Status::MS_STAT_ENERGY, 5);
                    break;
                case 2:
                    if ($sleeping) Globals::CurrentPlayerActual()->log()->add('Du hast die perfekte Ruheposition gefunden. Weil du jetzt so bequem liegst erhälst du einen Energieschub.');
                    else Globals::CurrentPlayerActual()->log()->add('In deiner Hose findest du eine alte Kinokarte von einem Film, den du dir mit Freunden angesehen hast. Diese schöne Erinnerung verschafft dir einen Energieschub.');
                    Globals::CurrentPlayerActual()->get_status()->modify(Model_Status::MS_STAT_ENERGY, 15);
                    break;
                case 3: Globals::CurrentPlayerActual()->log()->add('Eine Sternschnuppe! So eine hast du schon ewig nicht mehr gesehen. Dieser wunderschöne Anblick gibt dir Hoffnung und einen gewaltigen Energieschub!');
                    Globals::CurrentPlayerActual()->get_status()->modify(Model_Status::MS_STAT_ENERGY, 50);
                    break;
            }
        }


        return true;
    }



    public function enter($pid = null, $type = Interface_Tickable::IT_TYPE_PLAYER) {
        if (!$pid) $player = Globals::CurrentPlayer();
        elseif ($type == Interface_Tickable::IT_TYPE_PLAYER) $player = Globals::CurrentGame()->get_player($pid);
        else $player = Globals::CurrentGame()->get_npc($pid);

        parent::enter($pid, $type);
        new Model_Buffs_Home($player);
    }

    public function leave($pid = null, $type = Interface_Tickable::IT_TYPE_PLAYER) {
        if (!$pid) $player = Globals::CurrentPlayer();
        elseif ($type == Interface_Tickable::IT_TYPE_PLAYER) $player = Globals::CurrentGame()->get_player($pid);
        else $player = Globals::CurrentGame()->get_npc($pid);

        if (!parent::leave($pid, $type)) return false;
        //ToDo: Rooms
        if ($this->room()->has_content('defimp') && !$this->room()->has_content('impaler') && $type == Interface_Tickable::IT_TYPE_PLAYER)
        {
            $this->room()->add_content('impaler');
            $player->log()->add(new Model_Log_Types_Text(null, null, 'Auf dem Weg nach draußen hast du die Fallgrube wieder geschlossen und für einen erneuten Einsatz bereit gemacht.'));
        }

        if ($buff = $player->get_status()->retrieve('home')) $buff->unbuff();

        return true;
    }

    protected $defense = 5;

    protected $decay = 1;
    protected $patchup = 1;

    public function get_defense($actual = false) {
        return round($this->defense * ($actual ? 1 : (1 - $this->decay)));
    }

    public function get_decay() {
        return $this->decay;
    }

    public function get_patchup() {
        return $this->patchup;
    }


    public function set_decay($val, $absolute = true) {
        if ($absolute)
            $this->decay = $val;
        else $this->decay += $val;

        $this->decay = min(1,max(0, $this->decay));
    }

    public function set_patchup($val, $absolute = true) {
        if ($absolute)
            $this->patchup = $val;
        else $this->patchup += $val;

        $this->patchup = max(0, $this->patchup);
    }

    public function inc_defense($val) {
        $this->defense += $val;
        if ($this->defense < 1) $this->defense = 1;
    }
}	