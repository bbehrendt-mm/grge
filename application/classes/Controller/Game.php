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
            // Check vitem
            $action['remaining'] = $v_item ? ($v_item->remaining_actions($action['action']) < PHP_INT_MAX ? $v_item->remaining_actions($action['action']) : -1 ) : -1;

            // Translate
            foreach (['description', 'tooltip'] as $t)
                if ($action[$t]) $action[$t] = __($action[$t]);
            // Clean
            foreach (['action'] as $t)
                unset ($action[$t]);
        }

        return $actions;
    }

    /**
     * Renderer Subroutine; Render Location Box
     * @throws Exception
     */
    private function render_location() {
        /** @global Model_Player $player */
        global $player;

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

        // Add render data
        $this->add_data('location', [
            'meta' => [
                'name' => __($player->location()->name()),
                'desc' => __($player->location()->description()),
                'outside' => $player->location()->is_outside(),
                'css' => $player->location()->getCustomStyle(),
            ],
            'actions' => $a,
            'hideout' => $hideout ? [
                'state' => 100 * round(1 - $hideout->get_decay(), 2),
                'max_defense' => $hideout->get_defense(true),
                'defense' => $hideout->get_defense(false),
                'deco' => $hideout->get_deco(false),
                'max_deco' => $hideout->get_deco(true),
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
     * @return array
     */
    private function group_itemlist($itemlist) {
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
                'description' => __($item->description()),
                'icon' => $item->icon(),
                'weight' => $item->weight(),
                'actions' => $this->prepare_actionlist($item->auto_actions()),
                'addr' => substr(md5(get_class($item) . '__salt'), 0, 5),
                'flags' => $flags,
                'uin' => $item->uin(),
                'set' => [$item->uin()],
                'static' => 1,
                'stack' => __($item->stackname()),
                'label' => $item->label()
            ];

            if (Tool_System::instance_of($item, 'Interface_Countable') && !Tool_System::instance_of($item, 'Model_Items_Abstract_Bottle')) {
                $data['count'] = $item->count();
                $data['capacity'] = $item->capacity();
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

    private function render_inventory() {
        /**
         * @global $player Model_Player
         */
        global $player;

        // Get heroic actions
        $a = [];
        $action = false;
        /** @var Model_Buffs_Abstract_Fragile $buff */
        if (!($buff = $player->buff_retr('fragile')))
            foreach (Tool_Scripts::available_items('Model_Items_Abstract_Virtual',true,false,false,$player) as $a_item)
                /** @var  Model_Items_Abstract_Virtual $a_item */
                $a = array_merge($a,$this->prepare_actionlist($a_item->auto_actions(), $a_item));
        else $action = [
            'name' => __($buff->name()),
            'desc' => __($buff->description()),
            'abort' => $buff->abortable(),
            'remaining' => $buff->lifetime() > 0 ? Tool_Numerics::duration_to_split($buff->lifetime()) : false
        ];

        /** @noinspection PhpVoidFunctionResultUsedInspection */
        /** @noinspection PhpUndefinedMethodInspection */
        $this->add_data('inventory', [
            'player' => $this->group_itemlist($player->inventory()->get()),
            'weight' => [$player->inventory()->weight(),$player->inventory()->limit()],
            'location' => $this->group_itemlist($player->location()->inventory()->get()),
            'home' => (bool)Tool_Scripts::current_location_hideout(),
            'heroics' => $a,
            'action' => $action
        ]);
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

    private function status($type) {
        /**
         * @global $player Model_Player
         */
        global $player;

        return [
            'value' => round($player->stats_get($type),2),
            'buffs' => $this->condense_buff($type)
        ];
    }

    private function render_status() {
        /**
         * @global $player Model_Player
         */
        global $player;

        $cache = [];
        $tmp = 0;
        for ($type = 1; $type <= Model_Player::MP_STATUS_COUNT; $type++)
            if ($type <= 5 || $player->stats_get($type))
                $cache[$type] = $this->status($type);

        $buffs = [];
        foreach ($player->buff_get() as $buff) if ($buff->visible())
            $buffs[] = ['icon' => $buff->icon(), 'name' => __($buff->name()), 'desc' => __($buff->description())];

        $this->add_data('status', [
            'bars' => $cache,
            'buffs' => $buffs
        ]);
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
                    'energy' => $battle_ai[Model_Player::MP_SETTINGS_BATTLE_NOENERGY],
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