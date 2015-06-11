<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Gamemaster extends Controller {

    protected static $force_login = true;
    protected static $menu = 'logout';

    /**
     * Makes sure we don't try to use game starter with a game already running
     */
    public function before() {
        /** @global Model_User $user */
        global $user;

        //Do all the other before stuff
        parent::before();

        //Check if user already has a running game
        if ($user && $user->get_current_game()) {
            $this->error(\grge\E_STARTER_GAME_RUNNING);
            $this->request->action('noaction');
        }
    }

    /**
     * Creates a multiplayer game name in the given language
     * @param string $lang Language to use (de/en)
     * @return string Game name, or "##CONSTRUCTOR_LANG_MISSING##" if the language is invalid
     */
    private static function create_gamename($lang) {
        $list_a = array(
            'de' => array("Betrunkene", "Flinke",  "Gelähmte", "Euphorische", "Adoptierte", "Nackte", "Weise", "Letzte", "Verlassene", "Optimistische", "Pessimistische", "Konservative", "Liberale", "Französische", "Sozialistische", "Außerirdische", "Überflutete", "Fremde", "Geniale", "Erfahrene", "Verzweifelte", "Resignierte", "Hinkende", "Faule", "Abhängige", "Entspannte", "Abgebrannte", "Langweilige", "Unerfahrene", "Vereiste", "Verschwitzte"),
            'en' => array("Drunk", "Fast",  "Paralyzed", "Euphoric", "Adopted", "Nude", "Wise", "Last", "Lonely", "Optimistic", "Pessimistic", "Conservative", "Liberal", "French", "Socialistic", "Alien", "Flooded", "Foreign", "Smart", "Experienced", "Desperate", "Resigned", "Limping", "Lazy", "Addicted", "Relaxed", "Burned", "Boring", "Inexperienced", "Frozen", "Sweaty"),
        );

        $list_b = array(
            'de' => array("Alkoholiker", "Wiesel", "Unfallopfer", "Mädchen", "Chinesen", "Sexsymbole", "Bartträger", "Überlebende", "Liebhaber", "Gewinner", "Verlierer", "Parteianhänger", "Demonstranten", "Bürger", "Kommunisten", "Eroberer", "Grillmeister", "Zombies", "Bäcker", "Journalisten", "Touristen", "Säcke", "Weltkriegsveteranen", "Kiffer", "Entrepreneure", "Präsidenten", "Deutsche", "Amerikaner", "Briten", "Todesdackel", "Meerjungfrauen", "Konformisten", "Hipster", "Fettsäcke", "Nerds"),
            'en' => array("Alcoholic", "Weasels", "Accident Victims", "Girls", "Chinese", "Sex Symbols", "Bearded Men", "Survivors", "Love Machines", "Winners", "Loosers", "Party members", "Protesters", "Citizens", "Communists", "Conquerors", "Chefs", "Zombies", "Bakers", "Journalists", "Tourists", "Sacks", "World War Veterans", "Stoners", "Entrepreneurs", "Presidents", "Germans", "Americans", "Brits", "Wiener Dogs Of Ultimate Destruction", "Mermaids", "Conformists", "Hipsters", "Fatsos", "Nerds"),
        );

        if (!isset($list_a[$lang]) || !isset($list_b[$lang]))
            return "##CONSTRUCTOR_LANG_MISSING##";

        $a = Tool_Gambling::select($list_a[$lang]);
        $b = Tool_Gambling::select($list_b[$lang]);

        return "{$a} {$b}";
    }

    /**
     * Closes a player slot in the game lobby for a given game id.
     * @param int $id Game ID
     * @return bool True when decreasing the amount of slots was successful; otherwise false.
     */
    private static function fill_lobby_slot($id) {
        // Get lobby entry
        $data = DB::select('slots')->from('multiplayer_lobby')->where('slots', '>', 0)->where('gameid', '=', $id)->execute()->as_array();

        // Check if entry exists
        if (count($data) != 1)
            return false;

        // Set new value, or delete entry when number of slots is zero
        if ($data[0]['slots'] > 1)
            DB::update('multiplayer_lobby')->set(array('slots' => $data[0]['slots'] - 1))->where('gameid', '=', $id)->execute();
        else DB::delete('multiplayer_lobby')->where('gameid', '=', $id)->execute();

        return true;
    }

    /**
     * Create a single player game and add the current user as player. Note that this function does NOT attempt to validate the configuration!
     * @param int $mode Game mode
     * @param int $speed Game flow setting; duration of ticks in seconds, or -1 to use variable flow
     * @param int $job Profession
     * @param int $level Profession level
     * @param Model_Store_Interface[] $store
     * @return bool Always returns true
     * @throws Exception
     */
    private function start_singleplayer($mode,$speed,$job,$level,$store) {
        global $player;

        // Make a new game
        $game = new Model_Game();
        if (!$game->start($mode, ($speed < 0) ? 1 : 0, ($speed < 0) ? 300 : $speed, null))
            return $this->error(\grge\E_STARTER_CREATION_FAILED);

        foreach ($store as $elem)
            $elem::trigger_player_before_init($job,$level);

        // Join newly created game
        if (!$game->join($job, $level, null))
            return $this->error(\grge\E_STARTER_JOIN_FAILED);

        foreach ($store as $elem) {
            $elem::trigger_game_after_init($game);
            $elem::trigger_player_after_init($player);
        }

        // Update session and redirect
        $this->session->set('game',$game);
        $this->render(['redirect' => 'game/redirect']);
        return true;
    }

    /**
     * Create a multi player game and add the current user as player. Note that this function does NOT attempt to validate the configuration!
     * @param int $mode Game mode
     * @param int $job Player profession
     * @param int $level Player Profession level
     * @param string $name Game name
     * @param string $lang Language name
     * @param int $slots Number of open slots
     * @param string|bool $pw Password, or anything that equals false to disable password protection
     * @param Model_Store_Interface[] $store
     * @return bool
     * @throws Exception
     * @throws Kohana_Exception
     */
    private function start_multiplayer($mode,$job,$level,$name,$lang,$slots,$pw,$store) {
        global $player;

        // Check if lang is valid
        if (!in_array($lang, array_keys(static::get_lang_flags())))
            return $this->error(\grge\E_STARTER_INVALID_SETUP);

        // Create game
        $game = new Model_Game();
        if (($id = $game->start($mode, 1, 300, null, $name)) && DB::insert('multiplayer_lobby', array('gameid', 'lang', 'slots', 'name', 'timestamp', 'password'))->values(array($id, $lang, $slots, $name, time(), $pw ? hash('sha256', $pw, false) : null))->execute() ) {

            foreach ($store as $elem)
                $elem::trigger_player_before_init($job,$level);

            //Join game
            if (!$game->join($job, $level, null))
                return $this->error(\grge\E_STARTER_JOIN_FAILED);

            foreach ($store as $elem) {
                $elem::trigger_game_after_init($game);
                $elem::trigger_player_after_init($player);
            }

            DB::update('multiplayer_lobby')->set(array('slots' => $slots - 1))->where('gameid', '=', $id)->execute();
        } else return $this->error(\grge\E_STARTER_CREATION_FAILED);

        // Update session and redirect
        $this->session->set('game',$game);
        $this->render(['redirect' => 'game/redirect']);
        return true;
    }

    /**
     * Join an existing multi player game. Note that this function does NOT attempt to validate the configuration!
     * @param $id
     * @param int $job Player profession
     * @param int $level Player Profession level
     * @param string|bool $pw Password, can be empty if the game is not password protected
     * @param Model_Store_Interface[] $store
     * @return bool
     * @throws Exception
     */
    private function join_multiplayer($id,$job,$level,$pw,$store = []) {
        /**
         * @global Model_EUser $user
         */
        global $user, $player;

        // Check password
        if (!$this->check_password($id,$pw,false))
            return $this->error(\grge\E_STARTER_INVALID_SETUP);

        // Check banns
        if (!$pw && $user->lockouts_is_locked())
            return $this->error(\grge\E_STARTER_PLAYER_BANNED);

        //Check slots
        if (!static::fill_lobby_slot($id))
            return $this->error(\grge\E_STARTER_LOBBY_UPDATE_FAILURE);

        //Apply player mods
        foreach ($store as $elem)
            $elem::trigger_player_before_init($job,$level);

        // Load game
        $game = new Model_Game;
        if (!$game->read($id))
            return $this->error(\grge\E_STARTER_FETCH_FAILED);

        // Join player
        if (!$game->join($job, $level, null))
            return $this->error(\grge\E_STARTER_JOIN_FAILED);

        foreach ($store as $elem) {
            $elem::trigger_game_after_init($game);
            $elem::trigger_player_after_init($player);
        }

        // Update session and redirect
        $this->session->set('game',$game);
        $this->render(['redirect' => 'game/redirect']);
        return true;
    }

    /**
     * Automatically starts games to fill up the public multiplayer lobby
     * @throws Exception
     * @throws Kohana_Exception
     */
    private function fill_multiplayer_lobby() {
        global $game;

        // Get games matching the auto-fill language that have no password
        foreach (Kohana::$config->load('basic.multiplayer.parallel_games') as $lang => $count) {
            $num = DB::select(array(DB::expr('COUNT(*)'), 'num'))->from('multiplayer_lobby')->where('slots', '>', 0)->where('lang', '=', $lang)->and_where('password','=',null)->execute()->as_array();
            $num = (int)$num[0]['num'];

            // As long there is not enough of them, make more games
            while($num < $count) {
                $name = static::create_gamename($lang);
                $game = new Model_Game(false);
                if (($id = $game->start(10000, 1, 300, null, $name)) && DB::insert('multiplayer_lobby', array('gameid', 'lang', 'slots', 'name', 'timestamp'))->values(array($id, $lang, Kohana::$config->load('basic.multiplayer.capacity'), $name, time()))->execute() ) {
                    $num++;
                    $game = null;
                } else break;
            }
        }
    }

    /**
     * Returns a list of available flag icons
     * @return array
     */
    private function get_lang_flags() {
        $ret = [];
        foreach (scandir(APPPATH . 'assets/media/icons/lang') as $f)
            if (preg_match('/(\w+).png$/',$f,$matches))
                $ret[$matches[1]] = strtoupper($matches[1]);
        return $ret;
    }

    /**
     * Checks the password for a game; when no password is set, this will always return true
     * @param number $id Game ID
     * @param string $pw Password
     * @param bool $graceful_fail Value to return in case the game doesn't exist
     * @return bool True, when the password matches or there is no password; false, when the password doesn't match; $graceful_fail, when the game does not exist
     */
    private function check_password($id, $pw, $graceful_fail = false) {
        // Get lobby entry
        $data = DB::select('password')->from('multiplayer_lobby')->where('slots', '>', 0)->where('gameid', '=', $id)->execute()->as_array();

        // In case of missing entry, fail
        if (count($data) != 1)
            return $graceful_fail;

        // Check PW
        return (!$data[0]['password'] || $data[0]['password'] == hash('sha256', $pw, false));
    }

    /**
     * Checks game parameters for validity
     * @param int $mode Game mode; is checked for existence and lock status
     * @param int $job Player profession; is checked for existence, lock status and if it is a valid profession for the given game mode
     * @param int $flow Tick length; is checked for existence and validity in relation to the game mode
     * @param int $slots Number of open slots; is only checked for multiplayer games; checked for being in the range for selected game mode
     * @param int $id Game ID; must be nagative for a newly created game that does not have an ID yet
     * @param string $name Game name; Only checked for multiplayer games; checked for length
     * @return bool|int Returns false if the given setting is invalid; otherwise, returns the player profession level (at least 1)
     */
    private function check_game_params($mode,$job,$flow,$slots,$id,$name) {
        $startup = ($id < 0);

        //Get config
        $config = Tool_Gamemodes::compile_mode_database(true);

        //Check if mode is valid
        if (!isset($config['modes'][$mode]) || $config['modes'][$mode]['locked'])
            return false;

        //Check if job is valid
        if (!in_array($job, $config['modes'][$mode]['jobs']) || !isset($config['jobs'][$job]) || $config['jobs'][$job]['locked'])
            return false;

        //Check if job is unstartable
        if ($startup && isset($config['modes'][$mode]['unstartable_jobs']) && in_array($job, $config['modes'][$mode]['unstartable_jobs']))
            return false;

        //Check slots
        if ($startup && $config['modes'][$mode]['type'] == 'multi_custom' && ($slots < $config['modes'][$mode]['slots'][0] || $slots > $config['modes'][$mode]['slots'][1]))
            return false;

        //Check speed
        if ($flow > 0 && (!in_array($flow,[15,30,60,120,300,600,900]) || $config['modes'][$mode]['type'] != 'single'))
            return false;

        //Check name
        if ($startup && $config['modes'][$mode]['type'] == 'multi_custom' && (strlen($name) < 3 || strlen($name) > 96))
            return false;

        return max(1,(int)$config['jobs'][$job]['level']);
    }

    /**
     * Password check API
     */
    public function japi_check_pw() {
        $pw = $this->request->current()->post('password');
        $id = (int)$this->request->current()->post('id');
        $this->render(['proceed' => $this->check_password($id,$pw)]);
    }

    /**
     * Game start API
     * @return bool
     */
    public function japi_start() {
        /**
         * @global Model_EUser $user
         */
        global $user;

        // Get POST stuff
        $mode = (int)$this->request->current()->post('mode');
        $job = (int)$this->request->current()->post('job');
        $id = (int)$this->request->current()->post('id');
        $pw = $this->request->current()->post('password');
        $protect = $this->request->current()->post('protect');
        $flow = (int)$this->request->current()->post('flow');
        $name = preg_replace('/[^\w &.,!?\-\+:\/@\(\)=;\|]/', ' ', $this->request->current()->post('name'));
        $slots = (int)$this->request->current()->post('slots');
        $lang = $this->request->current()->post('lang');

        $store = $this->request->current()->post('store');

        // Check if all that config stuff is valid
        if (!($level = $this->check_game_params($mode,$job,$flow,$slots,$id,$name)))
            return $this->error(\grge\E_STARTER_INVALID_SETUP);

        $list = []; $current_payment = 0;
        if (Kohana::$config->load('balancing.shop.enabled')) {
            $current_payment = -Kohana::$config->load('balancing.shop.free_coins');
            if (is_array($store) && isset($store['purchase']) && isset($store['authorized_payment']) && is_array($store['purchase'])) {
                $max_payment = (int)$store['authorized_payment'];
                foreach (Tool_Gamemodes::get_store_classes() as $store_element)
                    if ($store_element::is_valid_for($mode,$job,($id <= 0),$id,$flow) && in_array(Tool_System::getClassID($store_element), $store['purchase'])) {
                        $list[] = $store_element;
                        $current_payment += $store_element::get_cost();
                    }

                $current_payment = max(0,$current_payment);
                if ($current_payment > $max_payment || $current_payment > $user->coins()) return $this->error(\grge\E_STARTER_INVALID_SETUP);
            }
        }

        // If an ID is given, we want to join a multiplayer game
        if ($id > 0) {
            if ($this->join_multiplayer($id,$job,$level,$pw,$list))
                return $user->remove_coins($user->uid(),$current_payment);
            else return false;
        }
        // If a name is given, we want to create a multiplayer game
        if ($name) {
            if ($this->start_multiplayer($mode,$job,$level,$name,$lang,$slots,$protect,$list))
                return $user->remove_coins($user->uid(),$current_payment);
            else return false;
        }
        // Otherwise, we probably want to create a single player game
        if ($this->start_singleplayer($mode,$flow,$job,$level,$list))
            return $user->remove_coins($user->uid(),$current_payment);
        else return false;
    }

    public function japi_eshop() {
        // Get POST stuff
        if (!Kohana::$config->load('balancing.shop.enabled')) return;

        $mode = (int)$this->request->current()->post('mode');
        $job = (int)$this->request->current()->post('job');
        $id = (int)$this->request->current()->post('id');
        $flow = (int)$this->request->current()->post('flow');
        $init = $this->request->current()->post('init') == '1';

        $store = [];
        foreach (Tool_Gamemodes::get_store_classes() as $store_element)
            if ($store_element::is_valid_for($mode,$job,$init,$id,$flow))
                $store[] = [
                    'id' => Tool_System::getClassID($store_element),
                    'cost' => $store_element::get_cost(),
                    'name' => __($store_element::get_name()),
                    'desc' => __($store_element::get_description()),
                    'cat' => __($store_element::get_type()),
                    'icon' => $store_element::get_icon(),
                ];

        usort($store, function($a,$b) {
            return ($a['cat'] == $b['cat']) ? ($a['cost'] - $b['cost']) : strcmp($a['cat'],$b['cat']);
        });

        $this->render(['store' => $store]);
    }

    private function convert_requirements($req) {

        $ret = [];
        foreach ($req['mode'] as $modeblock => $pair) {
            $modes = explode(',', "$modeblock");
            foreach ($modes as $c => $m)
                $modes[$c] = __(Tool_Gamemodes::get_board_by_id($m));
            if (count($modes) == 1)
                $modes = $modes[0];
            else $modes = implode(', ', array_slice($modes,0,-1)) . ' ' . __('oder') . ' ' . array_slice($modes,-1,1)[0];
            $ret[] = [
                'name' => __('SP in :modes', [':modes' => $modes]),
                'unlocked' => $pair[0] >= $pair[1],
                'text' => "{$pair[0]} / {$pair[1]}"
            ];
        }

        foreach ($req['job'] as $jobblock => $pair) {
            $jobs = explode(',', "$jobblock");
            foreach ($jobs as $c => $j)
                $jobs[$c] = __(Tool_Gamemodes::get_job_by_id($j));
            if (count($jobs) == 1)
                $jobs = $jobs[0];
            else $jobs = implode(', ', array_slice($jobs,0,-1)) . ' ' . __('oder') . ' ' . array_slice($jobs,-1,1)[0];
            $ret[] = [
                'name' => __('SP als :jobs', [':jobs' => $jobs]),
                'unlocked' => $pair[0] >= $pair[1],
                'text' => "{$pair[0]} / {$pair[1]}"
            ];
        }

        foreach ($req['ext'] as $id => $func) {
            $ret[] = [
                'name' => __($req['ext_notes']),
                'unlocked' => $func(),
                'text' => ''
            ];
        }

        return $ret;
    }

    /**
     * Game Creator Renderer
     * @throws Exception
     * @throws Kohana_Exception
     */
    public function action_lobby() {
        /** @global Model_Euser $user */
        global $user, $game;

        // Make sure there are enough open games in the lobby
        $this->fill_multiplayer_lobby();

        // Get active games; if user is banned, filter out public ones
        $data = DB::select()->from('multiplayer_lobby')->where('slots', '>', 0);
        if ($user->lockouts_is_locked()) $data->where('password', 'IS NOT', NULL);
        $data = $data->order_by('password', 'ASC')->order_by('lang')->execute()->as_array();

        // Iterate over each entry
        foreach ($data as $k => &$entry) {
            // Make sure ID and slots are stored as int
            foreach (['gameid','slots'] as $key)
                $entry[$key] = (int)$entry[$key];
            // Remove exact timestamp; the client doesn't need that
            unset($entry['timestamp']);
            // Don't send the password hash; just store if we have a password or not
            $entry['password'] = (bool)$entry['password'];

            // Load game to get more info
            $entry['locked'] = false;
            $local_game_obj = new Model_Game();
            // If we can't load the game, lock it
            if (!$local_game_obj->read($entry['gameid'], false)) {
                unset($data[$k]);
                continue;
            }

            // Get game mode
            $entry['mode'] = $local_game_obj->setting_mode();

            // Get players
            $entry['players'] = array();
            foreach ($local_game_obj->players(false) as $p) if ($p) {
                $entry['players'][] = array('name' => $p->name(), 'id' => (int)$p->id(), 'job' => $p->job(), 'cod' => $p->alive() ? null : $p->get_cod());
                if ($p->id() == $user->uid())
                    $entry['locked'] = true;
            } else $entry['locked'] = true;
        }
        $game = null;

        $database = Tool_Gamemodes::compile_mode_database(true);
        foreach ($database['modes'] as &$db_mode)
            $db_mode['requirements'] = $this->convert_requirements($db_mode['requirements']);
        foreach ($database['jobs'] as &$db_job)
            $db_job['requirements'] = $this->convert_requirements($db_job['requirements']);

        $this->dump('db', $database);

        // Render
        $this->add_widget(View::factory('pages/gameselect')
                ->set('database', $database)
                ->set('games', $data)
                ->set('languages', static::get_lang_flags())
                ->set('lock_count', $user->lockouts_get_count())
                ->set('lock_max', Kohana::$config->load('basic.multiplayer.mp_lockouts.max_count'))
                ->set('lock', $user->lockouts_is_locked())
                ->set('lock_timerange', $user->lockouts_get_time_range())
                ->set('show_shop', Kohana::$config->load('balancing.shop.enabled'))
                ->set('freecoins',Kohana::$config->load('balancing.shop.free_coins'))
                ->set('braincoins', $user->coins())
                ->render()
        );
        $this->render();

    }

}