<?php defined('SYSPATH') OR die('No direct access allowed.');

abstract class Model_Places_Abstract_Hideout extends Model_Places_Abstract_Place {

    protected static $outside = false;

    //Base: 15% per day
    protected static $decay_rate = 0.15;

    //Exp: 8% per day
    protected static $decay_exp = 0.08;

    protected static $widget_list = Array(
        'defense',
        'description',
    );

    protected $extensions = Array();
    protected $survival_find = true;
    protected $upgradable = true;

    protected $mapnodes = Array(-1 => 'Versteck verlassen');
    protected $breakins = 0;

    public function uin($uin = NULL) {
        if ($uin === NULL) return parent::uin();
        else $t = parent::uin($uin);

        $this->inventory->add(new Model_Items_Virtual_Location_Hideout(!$this->home_extensions("hideout", "cursed")));
        return $t;
    }

    public function home_extensions($type, $ext, $set = null) {
        if (!isset($this->extensions[$type])) $this->extensions[$type] = Array();
        if (!isset($this->extensions[$type][$ext])) $this->extensions[$type][$ext] = false;

        if ($set === null) return $this->extensions[$type][$ext];
        else $this->extensions[$type][$ext] = $set;

        return true;
    }

    public function pretick() {
        //Decay
        if ($this->decay < 1) {
            $this->set_decay(static::$decay_rate * (1/288) * $this->patchup * (1 + floor($this->zombie_factory->get_zombie_accumulation())/5), false);
            $this->set_patchup(static::$decay_exp * (1/288), false);
        }

        //Check for zombie attack
        if (($this->get_defense() < 1) && ($battle_log = Tool_Scripts::battle($this->zombie_factory->spawn_zombies(), Tool_Scripts::at_location($this->uin), true, $battle, $zc))) {
            $this->log->add(new Model_Log_Types_Battle(':zombiestr tauchen auf!', $battle_log, array(':zombiestr' => '<span class="value"><img src="/application/assets/icons/zombie.gif" />' . $zc . ' ' . __('Zombies') . '</span>')));
            return;
        }

        //Accumulate zombies
        $this->zombie_factory->accumulate_zombies();

        if (($this->get_defense() > 0) && floor($this->zombie_factory->get_zombie_accumulation()) > $this->get_defense()) {
            if ($this->home_extensions("bed", "wire")) {
                $this->home_extensions("bed", "wire", false);
                foreach (Tool_Scripts::at_location($this->uin) as $s_player)
                    if ($s_player->buff_retr('sleep_cozy')) {
                        $s_player->buff_retr('sleep_cozy')->unbuff();
                        new Model_Buffs_Exited($s_player->id(), 4);
                        $s_player->log()->add('Du hörst den Alarmdraht klingen und springst aus dem Bett, um dich gegen Zombies zu verteidigen!');
                    }
            }

            if ($battle_log = Tool_Scripts::battle($this->zombie_factory->release_zombie_population(), Tool_Scripts::at_location($this->uin), true, $battle, $num)) {
                $this->breakins++;

                foreach (Tool_Scripts::at_location($this->uin) as $s_player)
                    $s_player->achievements()->achieve(Model_Achievement::MA_BREAK_INS, $num);

                $this->log->add(new Model_Log_Types_Battle('Die Zombies haben deine Verteidigung durchbrochen! :num Zombies dringen ein!', $battle_log, array(':num' => $num)));
            }
        }
    }

    public function tick() {
        /**
         * @global $game Model_Game
         * @global $player Model_Player
         */
        global $game, $player;

        if (Tool_Events::current($game->next_tick()) == 'halloween' && !$this->home_extensions("hideout", "cursed"))
            new Model_Buffs_Scarecrow($player->user_id());

        //Build chance array
        $chance = Array(Array('chance' => 1500, 'value' => 0),	//Nothing happens
            Array('chance' => 5, 'value' => 1),		//random small energy gain
            Array('chance' => 3, 'value' => 2),		//random medium energy gain
            Array('chance' => 1, 'value' => 3));	//random big energy gain

        //Act accordingly
        $sleeping = $player->buff_retr('sleep_cozy');
        switch (Tool_Gambling::roulette($chance))
        {
            case 1: if ($sleeping) $player->log()->add(new Model_Log_Types_Text(null, null, 'Du hattest eben einen schönen Traum. Das hat dir etwas zusätzliche Energie verschafft.'));
            else $player->log()->add(new Model_Log_Types_Text(null, null, 'Du hast soeben die Antwort auf eine philosophische Frage gefunden, die dich schon seit Jahren quält. Das hat dir etwas zusätzliche Energie verschafft.'));
                $game->stats(Model_Game::MGLS_Energy, 5);
                break;
            case 2: if ($sleeping) $player->log()->add(new Model_Log_Types_Text(null, null, 'Du hast die perfekte Ruheposition gefunden. Weil du jetzt so bequem liegst erhälst du einen Energieschub.'));
            else $player->log()->add(new Model_Log_Types_Text(null, null, 'In deiner Hose findest du eine alte Kinokarte von einem Film, den du dir mit Freunden angesehen hast. Diese schöne Erinnerung verschafft dir einen Energieschub.'));
                $game->stats(Model_Game::MGLS_Energy, 15);
                break;
            case 3: $player->log()->add(new Model_Log_Types_Text(null, null, 'Eine Sternschnuppe! So eine hast du schon ewig nicht mehr gesehen. Dieser wunderschöne Anblick gibt dir Hoffnung und einen gewaltigen Energieschub!'));
                $game->stats(Model_Game::MGLS_Energy, 50);
                break;
        }

        return true;
    }



    public function enter($pid = null) {
        /**
         * @global $game Model_Game
         * @global $player Model_Player
         */
        global $game;
        if (!$pid) global $player;
        else $player = $game->get_player($pid);
        parent::enter($pid);
        new Model_Buffs_Home($player->id());
        if (Tool_Events::current($game->next_tick()) == 'halloween' && !$this->home_extensions("hideout", "cursed"))
            new Model_Buffs_Scarecrow($player->user_id());
    }

    public function leave($pid = null) {
        /**
         * @global $game Model_Game
         * @global $player Model_Player
         */
        global $game;
        if (!$pid) global $player;
        else $player = $game->get_player($pid);

        if (!parent::leave($pid)) return false;

        if ($this->impaler == 2)
        {
            $this->impaler = 1;
            $player->log()->add(new Model_Log_Types_Text(null, null, 'Auf dem Weg nach draußen hast du die Fallgrube wieder geschlossen und für einen erneuten Einsatz bereit gemacht.'));
        }

        if ($buff = $player->buff_retr('home')) $buff->unbuff();
        if ($buff = $player->buff_retr('scarecrow')) $buff->unbuff();

        return true;
    }

    protected function create_npcs() {
        global $game;
        $ret = parent::create_npcs();

        if (Tool_Events::current($game->next_tick()) == 'halloween' && !$this->home_extensions("hideout", "cursed"))
            $ret['halloween'] = Model_Npc::factory()->name('Grausame Vogelscheuche')
                ->add_action('Ansehen', Model_Action::factory()
                        ->effect(Model_Effect::factory()
                                ->message('Wer hat denn dieses gruselige Ding hier reingestellt? Die leeren Augen dieser Vogelscheuche sehen aus, als würden sie dich ständig anstarren... wie furchtbar!')
                        )
                )
                ->add_action('Gehirn einsetzen', Model_Action::factory()
                        ->grind_requirements(false)
                        ->requirement('Model_Items_Brainbox', 1)
                        ->condition(function($p) {
                            /** @var Model_Player $p */
                            if ($p->buff_retr('wow')) return false;
                            else return true;
                        })
                        ->show_as(Model_Effect::factory()
                                ->buff('Model_Buffs_Exited', false, 3)
                                ->ambiguous_effect()
                        )
                        ->fail_message('Dafür bist du im Moment zu aufgeregt.')
                        ->effect(Model_Effect::factory()
                                ->buff('Model_Buffs_Exited', false, 3)
                                ->spawn('Model_Items_Soul', 1)
                                ->message('Der Vogelscheuche ein Gehirn einzusetzen hat sie nicht wirklich weniger gruselig werden lassen... vor allem, weil sich das Gehirn vor deinen Augen aufgelöst und eine Seele freigesetzt hat!')
                        )
                )
            ;
        return $ret;
    }

    protected $defense = 5;

    protected $wallstrength = 0;
    protected $impaler = 0;
    protected $batgun = 0;

    protected $decay = 1;
    protected $patchup = 1;

    public function get_defense($actual = false) {
        return round($this->defense * ($actual ? 1 : (1- $this->decay)));
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
    }

    /**
     * Returns the base defense level
     * @return number
     */
    public function get_wallstr_level() {
        return min(ceil($this->wallstrength/3), 4);
    }

    /**
     * Increases the base defense level
     * @param int $dif Levels to increase (default: 1)
     */
    public function increase_wallstrength($dif = 1) {
        $this->wallstrength += $dif;
        $this->defense += $dif * 5;
    }

    /**
     * Returns weather any form of manual defense is available
     * @return bool
     */
    public function any_def() {
        return ($this->impaler == 1 || $this->batgun == 1);
    }

    public function get_impaler_level() {
        return $this->impaler;
    }

    public function get_batgun_level() {
        return $this->batgun;
    }

    public function defb_ready_impaler() {
        $this->impaler = 1;
    }

    public function defb_ready_batgun() {
        $this->batgun = 1;
    }

    public function use_impaler() {
        if ($this->impaler == 1) $this->impaler = 2;
    }

    public function defense_actions() {
        $ret = Array();
        $home = $this;

        if ($this->impaler == 1) $ret['impale'] = Array(
            'text'			=> 'Falltür öffnen',
            'short'         => 'Fallgruben',
            'energy'		=> 2,
            'requires'		=> Array(),
            'zombies_min'	=> floor($this->zombie_factory->get_zombie_accumulation()/2),
            'zombies_max'	=> floor($this->zombie_factory->get_zombie_accumulation()/2),
            'action'		=> function() use ($home) {$home->use_impaler();},
        );
        if ($this->batgun == 1) $ret['batg1'] = Array(
            'text'			=> 'Eine Batterie abfeuern',
            'short'         => 'Stationäres Batteriegeschütz',
            'energy'		=> 1,
            'requires'		=> Array('Model_Items_Battery' => 1),
            'zombies_min'	=> 1,
            'zombies_max'	=> 1 + floor($this->zombie_factory->get_zombie_accumulation()/10),
        );
        if ($this->batgun == 1) $ret['batg2'] = Array(
            'text'			=> 'Zwei Batterien abfeuern',
            'short'         => 'Stationäres Batteriegeschütz',
            'energy'		=> 2,
            'requires'		=> Array('Model_Items_Battery' => 2),
            'zombies_min'	=> 2,
            'zombies_max'	=> 2 * (1 + floor($this->zombie_factory->get_zombie_accumulation()/10)),
        );
        if ($this->batgun == 1) $ret['batg3'] = Array(
            'text'			=> 'Fünf Batterien abfeuern',
            'short'         => 'Stationäres Batteriegeschütz',
            'energy'		=> 5,
            'requires'		=> Array('Model_Items_Battery' => 5),
            'zombies_min'	=> 5,
            'zombies_max'	=> 5 * (1 + floor($this->zombie_factory->get_zombie_accumulation()/10)),
        );
        if ($this->batgun == 1) $ret['batg4'] = Array(
            'text'			=> 'Supercharger abfeuern',
            'short'         => 'Stationäres Batteriegeschütz',
            'energy'		=> 1,
            'requires'		=> Array('Model_Items_Generic_Supercharger' => 1),
            'zombies_min'	=> 5,
            'zombies_max'	=> 5 + floor($this->zombie_factory->get_zombie_accumulation()/3),
        );

        return $ret;
    }

    public function interaction_usedef($project) {
        /**
         * @global $game Model_Game
         * @global $player Model_Player
         */
        global $game, $player;

        if (floor($this->zombie_factory->get_zombie_accumulation()) == 0) {
            $player->log()->add(new Model_Log_Types_Text(null, null, 'Es ist verständlich dass du gerne irgend etwas töten möchtest... nur sind leider gerade keine Zombies in der Nähe.'));
            return false;
        }

        $build = $this->defense_actions();
        if (!isset($build[$project])) return false;

        if ($game->requirements($build[$project]['energy'], isset($build[$project]['requires']) ? $build[$project]['requires'] : Array())) {

            if (isset($build[$project]['action'])) $build[$project]['action']();

            $ret = min(mt_rand($build[$project]['zombies_min'], $build[$project]['zombies_max']), floor($this->zombie_factory->get_zombie_accumulation())) ;
            if ($ret > 0) {
                $this->zombie_factory->destroy_zombie_population($ret);
                $player->achievements()->achieve(Model_Achievement::MA_KILLED_ZOMBIES, $ret);

                if (isset($build[$project]['short']))
                    $p = $build[$project]['short'];
                else $p = $build[$project]['text'];

                $this->log->add(new Model_Log_Types_Built(Model_Log_Types_Built::MLTB_DEFENSE,$p, $player->id()), $ret);
                $player->log()->add(new Model_Log_Types_Text(null, null, 'Du hast :num Zombies vernichtet!', array(':num' => $ret)));
            } else $player->log()->add(new Model_Log_Types_Text(null, null, 'Das war wohl nichts ...'));

        } else $player->log()->add(new Model_Log_Types_Text(null, null, 'Diese Verteidigungsanlage ist momentan nicht einsatzbereit...'));
        return false;
    }

    protected function requirements($level, $type, $energy, $items, $callbacks = NULL) {
        global $game;

        return $game->requirements($energy, $items, $callbacks);
    }

    public function interaction_ktc($project) {
        return $this->build_project($project, 'kitchen', 'kitchen', 'Arbeit in der Küche abgeschlossen!', Model_Log_Types_Built::MLTB_KITCHEN);
    }

    public function interaction_power($project) {
        return $this->build_project($project, 'power', 'solar', 'Arbeit am Notstrom-Aggregat abgeschlossen!', Model_Log_Types_Built::MLTB_GENERATOR);
    }

    public function interaction_manu($project) {
        return $this->build_project($project, 'workshop', 'manu', 'Arbeit an der Werkbank abgeschlossen!', Model_Log_Types_Built::MLTB_WORKBENCH);
    }

    public function interaction_build($project) {
        /**
         * @global $player Model_Player
         */
        global $player;

        if (!$this->upgradable)
            return false;

        $buildcfg = Kohana::$config->load('blueprints.build.table.' . $project);

        if (!$buildcfg) return true;

        if (isset($buildcfg['energy']) && Tool_Scripts::get_timeofday() == "morning")
            $buildcfg['energy'] = round(max(0, $buildcfg['energy'] * 0.75));

        if ($this->requirements($buildcfg['condition']($this), NULL, $buildcfg['energy'], $buildcfg['requires']))
        {
            $buildcfg['action']($this);
            $player->achievements()->achieve(Model_Achievement::MA_CONSTRUCTIONS);
            if (isset($buildcfg['achievement'])) $player->achievements()->achieve($buildcfg['achievement']);

            if (isset($buildcfg['short']))
                $p = $buildcfg['short'];
            else $p = $buildcfg['text'];

            $this->log->add(new Model_Log_Types_Built(Model_Log_Types_Built::MLTB_BUILD,$p, $player->id()),null);
            $player->log()->add(isset($buildcfg['finalmsg']) ? $buildcfg['finalmsg'] : 'Bauarbeiten abgeschlossen!');
        }
        return true;
    }

    protected function build_project($project, $config_base, $symbol_name, $finalmsg, $symbol = 0) {
        global $player;
        $buildcfg = Kohana::$config->load('blueprints.' . $config_base . '.table.' . $project);

        if (!$buildcfg) return true;

        if (isset($buildcfg['energy']) && $config_base == 'workshop' && $this->home_extensions("manu", "susp"))
            $buildcfg['energy'] = round(max(0, $buildcfg['energy'] * 0.5));

        if (isset($buildcfg["environment"]))
            foreach ($buildcfg["environment"] as $base)
                if (!$this->home_extensions($symbol_name, $base)) return true;

        if ($this->requirements(true, true, $buildcfg['energy'], $buildcfg['requires']))
        {
            foreach($buildcfg['produces'] as $class => $count) {
                if (Tool_System::instance_of($class, 'Model_Items_Abstract_Stackable'))
                    $this->inventory->add(new $class($count));
                else for ($i = 0; $i < $count; $i++) $this->inventory->add(new $class);
            }
            if ($buildcfg['achievement']) $player->achievements()->achieve($buildcfg['achievement']);

            if (isset($buildcfg['short']))
                $p = $buildcfg['short'];
            elseif (isset($buildcfg['produces'])) {
                $c = array_keys($buildcfg['produces']);
                $c = $c[0];
                /** @noinspection PhpUndefinedMethodInspection */
                $p = $c::static_name();
            } else $p = $buildcfg['text'];

            $this->log->add(new Model_Log_Types_Built($symbol,$p, $player->id()),null);
            $player->log()->add(isset($buildcfg['finalmsg']) ? $buildcfg['finalmsg'] : 'Arbeit abgeschlossen!');
        }
        return true;
    }
}	