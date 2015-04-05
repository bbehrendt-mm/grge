<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Game extends Controller {

    protected static $force_login = true;
    protected static $menu = 'logout';

    protected static $death_allowed_actions = ['end'];

    /**
     * Hook for AJAX calls using JAPI
     * @return bool
     */
    public function action_japi() {
        /**
         * @global $game Model_Game
         * @global $player Model_Player
         */
        global $game, $player;

        if (!$game || !$player) return $this->render(['redirect' => 'landing/redirect']);
        if (!$player->alive() && !in_array($this->request->param('jaction'), static::$death_allowed_actions)) {
            $this->render_notifications();
            return $this->render(['redirect' => 'game/redirect']);
        }

        return parent::action_japi();
    }

    /**
     * @param Model_Hid[] $actions
     * @param Model_Items_Abstract_Virtual $v_item
     * @return mixed
     */
    private function prepare_actionlist($actions, $v_item = null) {
        // Prepare actions
        foreach ($actions as &$action) {
            // Check item
            $action['remaining'] = $v_item ? ($v_item->remaining_actions($action['action']) < PHP_INT_MAX ? $v_item->remaining_actions($action['action']) : -1 ) : -1;

            // Translate
            foreach (['description', 'tooltip'] as $t)
                if ($action[$t]) $action[$t] = __($action[$t]);
        }

        return $actions;
    }

    /**
     * Renderer Subroutine; Render Location Box
     * @throws Exception
     */
    private function render_location() {
        /**
         * @global $game Model_Game
         * @global $player Model_Player
         */
        global $game, $player;

        //ToDo: Radar effects
        // Get Radar data
        list($radar_min, $radar_max, $radar_prop, $radar_increase) = $player->location()->zombie_factory()->get_radar_data();

        // Check if we're at a hideout with active defenses
        $hideout = Tool_Scripts::current_location_hideout();
        $protected_hideout = $hideout && $hideout->get_defense() > 0;

        // Calculate approx. number of ticks between each blockade increase and random attack; set random attack value to zero if we're at a hideout
        if ($protected_hideout)
            $radar_prop = 0;
        else $radar_prop = ($radar_prop > 0) ? ceil(pow($radar_prop,-1)) : 0;
        $radar_increase = ($radar_increase > 0) ? ceil(pow($radar_increase,-1)) : 0;

        // Calculate danger level
        $danger = ($radar_prop > 0) ? floor($radar_max/4) : 0;              // Base value: Max attack group size
        if (!$protected_hideout && $radar_prop <= 1.5 && $radar_prop > 0)     $danger += 2;    // Increase by 2 if we have a very high attack probability
        elseif (!$protected_hideout && $radar_prop <= 3 && $radar_prop > 0)   $danger += 1;    // Increase by 1 if we have a high attack probability
        elseif ($radar_prop <= 15  || $radar_prop == 0)  $danger -= 1;                           // Decrease by 1 if we have a very low attack probability
        if ($radar_increase != 0 && $radar_increase <= 3)   $danger += 1;   // Increase by 1 if we have a very high blocking speed
        $danger = min(5,max(($radar_prop > 0) ? 1 : 0,$danger));            // Confine danger to 0-5 range

        // Get local actions
        $a = [];
        if (!$player->buff_retr('fragile'))
            foreach (Tool_Scripts::available_items('Model_Items_Abstract_Virtual',false,true,false,$player) as $a_item)
                /** @var  Model_Items_Abstract_Virtual $a_item */
                $a = array_merge($a,$this->prepare_actionlist($a_item->auto_actions(), $a_item));

        // Get doorways
        $doorways = array();
        foreach ($player->location()->get_doorways() as $did) {
            $doorways[$did]['location'] = __($game->location($did)->name());
            $doorways[$did]['name'] = __(Tool_Scripts::get_map_description($game->map($did)->get_sublocation()));
        }
        if (!count($doorways)) $doorways = false;

        // Add render data
        $this->add_data('location', [
            'meta' => [
                'name' => __($player->location()->name()),
                'desc' => __($player->location()->description()),
                'outside' => $player->location()->is_outside(),
                'css' => $player->location()->getCustomStyle(),
            ],
            'actions' => $a,
            'doorways' => $doorways,
            'hideout' => $hideout ? [
                'state' => 100 * round(1 - $hideout->get_decay(), 2),
                'max_defense' => $hideout->get_defense(true),
                'defense' => $hideout->get_defense(false),
                'deco' => $hideout->deco(null, false),
            ] : false,
            'radar' => [
                'danger' => $danger,
                'min' => 0,
                'max' => ceil($radar_max/8)*8,
                'prop' => $radar_prop * 5,
                'inc' => $radar_increase * 5,
                'hideout' => (bool)$hideout,
                'zombies' => $player->location()->zombie_pop()
            ]
        ]);
    }

    /**
     * @param $itemlist Model_Items_Abstract_Item[]
     * @param bool $short
     * @return array
     */
    private function group_itemlist($itemlist, $short = false) {
        $grouping = Array();
        $cache = Array();

        // iterate over item list
        foreach ($itemlist as $item)
        {
            // Create category array
            if (!isset($grouping[$item->cat()])) $grouping[$item->cat()] = Array('items' => Array(), 'name' => '');

            // Check if our item supports static stacking
            $static = Tool_System::instance_of($item, 'Interface_Static');
            // If we support stacking and a stack for this item exists, simply add to stack
            if ($static && isset($cache[get_class($item) . "/" . $item->icon() . "/" . $item->name()])) {
                $link = &$grouping[$item->cat()]['items'][$cache[get_class($item) . "/" . $item->icon() . "/" . $item->name()]];
                $link['static']++;
                $link['set'][] = $item->uin();
                continue;
            // If we support stacking and a stack for this item does not exists, create one
            } elseif ($static)
                $cache[get_class($item) . "/" . $item->icon() . "/" . $item->name()] = $item->uin();

            $flags = [];

            /** @noinspection PhpUndefinedMethodInspection */
            // Set item flags
            if (Tool_System::instance_of($item, 'Model_Items_Abstract_Armor') && $item->is_active())    $flags[] = 'equipped';
            if (Tool_System::instance_of($item, 'Model_Items_Abstract_Armor'))                          $flags[] = 'armor';
            if (Tool_System::instance_of($item, 'Model_Battle_Weapon'))                                 $flags[] = 'weapon';
            if (Tool_System::instance_of($item, 'Model_Items_Abstract_Escape'))                         $flags[] = 'escape';
            if (Tool_System::instance_of($item, 'Interface_Tmpitem'))                                   $flags[] = 'temp';
            if (Tool_System::instance_of($item, 'Interface_Event'))                                     $flags[] = 'event';
            if ($item->is_carrier_item())                                                               $flags[] = 'carrier';


            $data = [
                'name' => __($item->name()),
                'description' => $short ? '' : __($item->description()),
                'icon' => $item->icon(),
                'weight' => $item->weight(),
                'actions' => $short ? [] : $this->prepare_actionlist($item->auto_actions()),
                'addr' => Tool_System::getClassID($item),
                'flags' => $flags,
                'uin' => $item->uin(),
                'set' => [$item->uin()],
                'static' => 1,
                'stack' => __($item->stackname()),
                'label' => $item->label(),
                'deco' => $item->deco()
            ];

            // No item specifics for short
            if (!$short) {
                if (Tool_System::instance_of($item, 'Interface_Label'))
                    $data['custom_label'] = true;

                if (Tool_System::instance_of($item, 'Interface_Countable') && !Tool_System::instance_of($item, 'Model_Items_Abstract_Bottle')) {
                    $data['count'] = $item->count();
                    $data['capacity'] = $item->capacity();
                }

                if (Tool_System::instance_of($item, 'Model_Items_Ammobelt')) {
                    $tmp_ammo = [];

                    /** @var Model_Items_Ammobelt $item */
                    foreach ($item->contains() as $class => $value) {
                        /** @var Model_Items_Abstract_Ammo $class */
                        $tmp_ammo[] = [
                            'addr' => Tool_System::getClassID($class),
                            'icon' => $class::static_icon(),
                            'count' => $value
                        ];
                    }

                    $data['ammobelt'] = $tmp_ammo;
                }

                /** @var Interface_Fillable|Model_Items_Abstract_Bottle $item */
                if (Tool_System::instance_of($item, 'Interface_Fillable') || Tool_System::instance_of($item, 'Model_Items_Abstract_Bottle')) {
                    $data['count'] = (int)$item->fillrate();
                    $data['fill'] = [
                        'capacity' => $item->capacity(),
                        'fixed' => !Tool_System::instance_of($item, 'Model_Items_Abstract_Bottle')
                    ];
                }

                if (Tool_System::instance_of($item, 'Model_Items_Abstract_Liquid'))
                    $data['is_water'] = true;

                if (Tool_System::instance_of($item, 'Model_Items_Chem'))
                    $data['is_chem'] = true;

                if (Tool_System::instance_of($item, 'Model_Items_Abstract_Pillbox'))
                    $data['is_pillbox'] = true;
            }


            // Build final item object
            $grouping[$item->cat()]['items'][$item->uin()] = $data;
        }

        // Sort items based on their address
        foreach (array_keys($grouping) as $gid)
            uasort($grouping[$gid]['items'], function($a, $b) {return strcmp($a['addr'], $b['addr']);});

        // Set category strings
        foreach (array_keys($grouping) as $gid)
            $grouping[$gid]['name'] = __(Model_Items_Abstract_Item::translateCatID($gid));

        return $grouping;
    }

    /**
     * @param bool|Model_Player $remote
     * @return array|void
     */
    private function render_inventory($remote = false) {
        /**
         * @global $player Model_Player
         */
        global $player;
        $p = $remote ? $remote : $player;

        // Get heroic actions
        $a = [];
        $action = false;
        if (!$remote) {
            /** @var Model_Buffs_Abstract_Fragile $buff */
            if (!($buff = $p->buff_retr('fragile')))
                foreach (Tool_Scripts::available_items('Model_Items_Abstract_Virtual',true,false,false,$p) as $a_item)
                    /** @var  Model_Items_Abstract_Virtual $a_item */
                    $a = array_merge($a,$this->prepare_actionlist($a_item->auto_actions(), $a_item));
            else $action = [
                'name' => __($buff->name()),
                'desc' => __($buff->description()),
                'abort' => $buff->abortable(),
                'remaining' => $buff->lifetime() > 0 ? Tool_Numerics::duration_to_split($buff->lifetime()) : false
            ];
        }

        /** @noinspection PhpVoidFunctionResultUsedInspection */
        /** @noinspection PhpUndefinedMethodInspection */
        $tmp = [
            'player' => $this->group_itemlist($p->inventory()->get(), $remote ? true : false),
            'weight' => [$p->inventory()->weight(),$p->inventory()->limit()],
            'location' => $remote ? [] : $this->group_itemlist($p->location()->inventory()->get()),
            'home' => $remote ? false : (bool)Tool_Scripts::current_location_hideout(),
            'heroics' => $a,
            'action' => $action
        ];

        if ($remote) return $tmp;
        else return $this->add_data('inventory', $tmp);
    }

    private function condense_buff($bar) {
        /**
         * @global Model_Player $player
         */
        global $player;

        $ret = Array();
        foreach ($player->buff_get() as $buff) {
            $t = ['icon' => $buff->icon(), 'effects' => []];
            $apply = false;
            foreach ([Model_Buffs_Abstract_Buff::MB_RAISE_ACC, Model_Buffs_Abstract_Buff::MB_DROP_ACC, Model_Buffs_Abstract_Buff::MB_RAISE_PRC, Model_Buffs_Abstract_Buff::MB_DROP_PRC] as $id) {
                $effect = $buff->effect($bar, $id);
                $t['effects'][$id] = $effect;
                $apply = $apply || ($effect != 0);
            }
            if ($apply) $ret[] = $t;
        }

        return $ret;
    }

    /**
     * @param int $type
     * @param bool|Model_Player $remote
     * @return array
     */
    private function status($type, $remote = false) {
        /**
         * @global $player Model_Player
         */
        global $player;
        $p = $remote ? $remote : $player;


        return [
            'value' => round($p->stats_get($type),2),
            'buffs' => $remote ? [] : $this->condense_buff($type)
        ];
    }

    /**
     * @param bool|Model_Player $remote
     * @return array|void
     */
    private function render_status($remote = false) {
        /** @global $player Model_Player */
        global $player;

        $p = $remote ? $remote : $player;

        $cache = [];
        $tmp = 0;
        for ($type = 1; $type <= Model_Player::MP_STATUS_COUNT; $type++)
            if ($type <= 5 || $p->stats_get($type))
                $cache[$type] = $this->status($type, $remote);

        $buffs = [];
        foreach ($p->buff_get() as $buff) if ($buff->visible() && (!$remote || $buff->visible(true)))
            $buffs[] = ['icon' => $buff->icon(), 'name' => __($buff->name()), 'desc' => $remote ? '' : __($buff->description())];

        $tmp = [
            'bars' => $cache,
            'buffs' => $buffs
        ];

        if ($remote) return $tmp;
        else return $this->add_data('status', $tmp);
    }

    protected function render_notifications() {
        /**
         * @global $player Model_Player
         */
        global $player;

        foreach ($player->log()->get_all() as $message) {
            $r = $message->as_notification();
            if ($r) $this->add_note($r[0],$r[1],$r[2]);
        }

        $player->log()->clear();
    }

    protected function render_log() {
        /**
         * @global $player Model_Player
         */
        global $player;

        $ret = [];
        foreach ($player->location()->log()->get_all(true) as $message)
            $ret[] = array_merge(['new' => false], $message->render());
        $player->location()->log()->reset_news_counter();

        $this->add_data('log', $ret);
    }

    private function render_clock() {
        /**
         * @global $game Model_Game
         * @global $player Model_Player
         */
        global $game, $player;

        $this->add_data('clock', [
            'show' => $game->is_alive() && !$game->paused() && $player && $player->alive() && !Tool_Events::is_april_fools(),
            'next_tick' => $game->next_tick(),
            'last_tick' => $game->now(),
            'current' => time(),
            'ingame' => Tool_Scripts::get_daytime()->getTimestamp(),
        ]);
    }

    private function render_settings() {
        /**
         * @global $game Model_Game
         * @global $player Model_Player
         */
        global $game, $player;

        $tmp =  ($game->timeflow() == 0) ? [
            'interval' => (int)Kohana::$config->load('balancing.pause.min_interval'),
            'duration' => (int)Kohana::$config->load('balancing.pause.min_duration'),
        ] : [
            'game' => $game->tick_length(),
            'player' => $player->vote_time(),
            'selection' => [15,30,60,120,300,600,900],
            'interval' => (int)Kohana::$config->load('balancing.pause.min_duration'),
        ];

        $lock = (($game->timeflow() == 0) ? ($game->pauselock() + Kohana::$config->load('balancing.pause.min_interval')) : $player->vote_time(true)) - time();
        if ($lock < 0) $lock = false;

        $battle_ai = $player->get_battle_settings();

        $ammo_data = [];
        foreach (Controller_Player::battle_ai_ammo_types() as $ammo) {
            /** @var Model_Items_Abstract_Ammo|string $ammo */
            $ammo_data[] = [
                'icon' => $ammo::static_icon(),
                'name' => __($ammo::static_name()),
                'locked' => isset($battle_ai[$ammo]) ? (bool)$battle_ai[$ammo] : false
            ];
        }

        $this->add_data('settings', [
            'clock' => [
                'time_mode' => $game->timeflow(),
                'time_settings' => $tmp,
                'locked' => $lock
            ],
            'ai' => [
                'type' => $battle_ai[Model_Player::MP_SETTINGS_BATTLE_DISTANCE_DAMAGE_SHIFT],
                'weapons' => [
                    'energy' => $player->job(1080) ? 'locked' : $battle_ai[Model_Player::MP_SETTINGS_BATTLE_NOENERGY],
                    'throw' => $battle_ai[Model_Player::MP_SETTINGS_BATTLE_NOSELFAMMO],
                    'tank' => $battle_ai[Model_Player::MP_SETTINGS_BATTLE_NOTANKAMMO],
                ],
                'ammo' => $ammo_data
            ]
        ]);
    }

    private function render_specials() {
        /**
         * @global $game Model_Game
         * @global $player Model_Player
         */
        global $game, $player;

        // Colosseum
        if (Tool_System::instance_of($player->location(), 'Model_Places_Colosseum')) {
            /** @var Model_Places_Colosseum $colosseum */
            $colosseum = $player->location();
            $cfg = $colosseum->get_config();
            if ($cfg['distance'] < 10)		$arena = 0;
            elseif ($cfg['distance'] < 30)	$arena = 1;
            elseif ($cfg['distance'] < 60)	$arena = 2;
            else							$arena = 3;

            $this->add_data('location', ['colosseum' => [
                'level' => $colosseum->level(),
                'rank' => min(4,floor(($colosseum->level() - 1)/5) + 1),
                'arena' => $arena
            ]]);
        }

        //Last Scout
        if ($game->config('modules.mapping') && $player->inventory()->get('Model_Items_Maptool') && !Tool_System::instance_of($player->location(), 'Model_Places_Abstract_Xmas') && !Tool_System::instance_of($player->location(), 'Model_Places_Abstract_Hideout') && !Tool_System::instance_of($player->location(), 'Model_Places_Abstract_Node')) {
            /** @var Model_Items_Maptool $mapper */
            $mapper = $player->inventory()->get('Model_Items_Maptool'); $mapper = $mapper[0];

            $this->add_data('location', ['scouting' => [
                'level' => $mapper->get_map_details() * 33 + ($mapper->get_map_details() == 3 ? 1 : 0),
                'laser' => Tool_Scripts::count_available_items('Model_Items_Generic_Lasermapper'),
            ]]);
        }

        // Roadtrip
        if (Tool_System::instance_of($player->location(), 'Model_Places_Motorhome')) {
            /** @var Model_Places_Motorhome $motorhome */
            $motorhome = $player->location();

            $parts = [];
            foreach ($motorhome->get_parts() as $item => $status) {
                /** @var Model_Items_Abstract_Item $item */
                $parts[] = [
                    'icon' => $item::static_icon(),
                    'name' => $item::static_name(),
                    'addr' => Tool_System::getClassID($item),
                    'count' => $status[0],
                    'max' => $status[1],
                ];
            }

            $this->add_data('location', ['caravan' => [
                'stops' => $motorhome->get_level_progress(),
                'speed' => $motorhome->get_speed(true),
                'distance' => $motorhome->get_distance(),
                'parts' => $parts,
                'weight' => [$motorhome->weight(), $motorhome->weight_max()]
            ]]);
        }
    }

    private function render_mp() {
        /**
         * @global $game Model_Game
         * @global $player Model_Player
         */
        global $game, $player;

        if (!$game->config('modules.multiplayer')) return;

        $speeds = [15,30,60,120,300,600,900];
        $jokes = [
            'Letzter Unterhosenwechsel' => 'Vor 12 Wochen',
            'Höchster Schulabschluss' => 'Kindergarten',
            'Geschlechtskrankheiten' => '[nt]4',
            'Lieblingskünster' => 'Helene Fischer',
            'Lieblings-Item' => 'Massagestab',
            'Spieler-ID' => 'ID10T',
            'Browser' => 'Internet Explorer 6',
            'Betriebssystem' => 'MUTOS 1600',
        ];

        $players = [];
        foreach ($game->players(false) as $p) {
            if ($p->id() == $player->id()) continue;

            $joke = array_keys($jokes)[($game->id() + $p->id()) % count($jokes)];

            $local = $p->alive() && $p->location_class() == $player->location_class();
            $stats = [];
            if ($local)
                for ($type = 1; $type <= Model_Player::MP_STATUS_COUNT; $type++)
                    if ($type <= 5 || $player->stats_get($type))
                        $cache[$type] = $this->status($type);


            $players[$p->id()] = [
                'name' => $p->name(),
                'id' => $p->id(),
                'speed' => $speeds[$p->vote_time()],
                'local' => $local,
                'stats' => $local ? $this->render_status($p) : false,
                'inventory' => ($local && $p->companion()) ? $this->render_inventory($p) : false,
                'escort' => $local ? $p->companion() : false,
                'last_seen' => $p->last_action(),
                'job' => __(Tool_Gamemodes::get_job_by_id($p->job())),
                'joke' => [
                    0 => __($joke),
                    1 => __($jokes[$joke])
                ]
            ];
        }

        $this->add_data('players', [
            'others' => $players,
            'self' => [
                'escort' => $player->companion(),
                'ping' => $player->chat_beacon()
            ]
        ]);
    }

    /**
     * Renderer API
     * @throws Kohana_Exception
     */
    public function japi_data() {
        /**
         * @global $player Model_Player
         */
        global $player;

        if (!$player->alive()) {
            $this->render_notifications();
            return $this->render(['redirect' => 'game/redirect']);
        }

        $this->render_location();
        $this->render_inventory();
        $this->render_status();
        $this->render_settings();
        $this->render_clock();
        $this->render_log();
        $this->render_mp();
        $this->render_specials();
        $this->render_notifications();

        $version_data = Kohana::$config->load('build.version');
        $this->add_data('version', "{$version_data['major']}.{$version_data['minor']}.{$version_data['service']}-{$version_data['stage']}-{$version_data['maintenance']}-{$version_data['build']}");

        $this->render(false);
        return true;
    }

    /**
     * Load appropriate game interface (in-game/death/pause/aprils fools)
     */
    public function action_redirect() {
        /**
         * @global Model_Game $game
         * @global Model_Player $player
         */
        global $game, $player;

        //Redirect
        if (!$game)
            $this->redirect(URL::site('gamemaster/lobby', 'http'));
        if (!$player) {
            $this->session->delete('game');
            $this->redirect(URL::site('landing/redirect', 'http'));
        }

        //Check if player is alive
        if ($player->alive()) {
            $player->last_action(true);

            if ($game->paused())
                // Load pause screen
                //ToDo: Pause Page
                $this->add_widget(View::factory('pages/pause')->set('remaining', max(0,$game->pauselock() - (time() - Kohana::$config->load('balancing.pause.min_duration'))))->render());
            else
                // Ingame View
                $this->add_widget(View::factory('pages/ingame')->render());
        }
        //Show game summary
        else {
            $a_points = 0;
            $a_data = [];
            foreach ($player->achievements()->get_all() as $aid => $value) {
                $a_points += Model_Achievement::points_aid($aid) * $value;
                $a_data[$aid] = [
                    'id' => $aid,
                    'icon' => $aid . '.gif',
                    'name' => Model_Achievement::decode_aid($aid),
                    'class' => Model_Achievement::class_aid($aid),
                    'count' => $value
                ];
            }

            $this->add_widget(View::factory('pages/death')
                ->set('soul_points', $game->points($player->id()))
                ->set('ach_points', $game->points($player->id()))
                ->set('achievements', $a_data)
                ->set('rankable', $game->is_rankable())
                ->set('time', Tool_Numerics::duration_to_string($game->get_player($player->id())->get_lifetime()))
                ->set('split_time', Tool_Numerics::duration_to_split($game->get_player($player->id())->get_lifetime()))
                ->set('cause_of_death', $player->get_cod())
                ->render());
        }

        $this->render();
    }

    public function japi_end() {
        /**
         * @global $game Model_Game
         * @global $player Model_Player
         * @global $user Model_User
         */
        global $game, $user, $player;

        //Check if player is still alive
        if (!$player->alive())
        {
            //ToDo: Karma
            //foreach ($game->players(false) as $p) if ($p->id() != $player->id())
            //    Model_User::set_karma($p->id(), $player->id(), (int)$this->request->post('karma_' . $p->id()));

            //End game and delete game object from session
            if ($game->retire($user->uid())) {
                /** @noinspection PhpUndefinedMethodInspection */
                $this->session->delete('game');
                unset($GLOBALS['game']);
            }
        }

        //Redirect
        $this->render([
            'redirect' => 'lobby/main',
        ]);
    }

}