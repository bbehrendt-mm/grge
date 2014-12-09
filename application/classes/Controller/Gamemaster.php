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

        return "{$list_a[$lang][$a]} {$list_b[$lang][$b]}";
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
     * @return bool Always returns true
     */
    private function start_singleplayer($mode,$speed,$job,$level) {
        // Make a new game
        $game = new Model_Game();
        if (!$game->start($mode, ($speed < 0) ? 1 : 0, ($speed < 0) ? 300 : $speed, null))
            return $this->error(\grge\E_STARTER_CREATION_FAILED);

        // Join newly created game
        if (!$game->join($job, $level, null))
            return $this->error(\grge\E_STARTER_JOIN_FAILED);

        // Update session and redirect
        $this->session->set('game',$game);
        $this->render(['redirect' => 'game/redirect']);
        return true;
    }

    private function start_multiplayer($mode,$job,$level,$name,$lang,$slots,$pw) {
        // Check if lang is valid
        if (!in_array($lang, array_keys(static::get_lang_flags())))
            return $this->error(\grge\E_STARTER_INVALID_SETUP);

        // Create game
        $game = new Model_Game(false);
        if (($id = $game->start($mode, 1, 300, null, $name)) && DB::insert('multiplayer_lobby', array('gameid', 'lang', 'slots', 'name', 'timestamp', 'password'))->values(array($id, $lang, $slots, $name, time(), $pw ? hash('sha256', $pw, false) : null))->execute() ) {

            //Join game
            if (!$game->join($job, $level, null))
                return $this->error(\grge\E_STARTER_JOIN_FAILED);

            DB::update('multiplayer_lobby')->set(array('slots' => $slots - 1))->where('gameid', '=', $id)->execute();
        } else return $this->error(\grge\E_STARTER_CREATION_FAILED);

        // Update session and redirect
        $this->session->set('game',$game);
        $this->render(['redirect' => 'game/redirect']);
        return true;
    }

    private function join_multiplayer($id,$job,$level,$pw) {
        /**
         * @global Model_EUser $user
         */
        global $user;

        // Check password
        if (!$this->check_password($id,$pw,false))
            return $this->error(\grge\E_STARTER_INVALID_SETUP);

        // Check banns
        if (!$pw && $user->lockouts_is_locked())
            return $this->error(\grge\E_STARTER_PLAYER_BANNED);

        //Check slots
        if (!static::fill_lobby_slot($id))
            return $this->error(\grge\E_STARTER_LOBBY_UPDATE_FAILURE);

        // Load game
        $game = new Model_Game;
        if (!$game->read($id))
            return $this->error(\grge\E_STARTER_FETCH_FAILED);

        // Join player
        if (!$game->join($job, $level, null))
            return $this->error(\grge\E_STARTER_JOIN_FAILED);

        // Update session and redirect
        $this->session->set('game',$game);
        $this->render(['redirect' => 'game/redirect']);
        return true;
    }

    private function fill_multiplayer_lobby() {
        global $game;

        foreach (Kohana::$config->load('basic.multiplayer.parallel_games') as $lang => $count) {
            $num = DB::select(array(DB::expr('COUNT(*)'), 'num'))->from('multiplayer_lobby')->where('slots', '>', 0)->where('lang', '=', $lang)->execute()->as_array();
            $num = (int)$num[0]['num'];

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

    private function check_password($id, $pw, $graceful_fail = false) {
        $data = DB::select('password')->from('multiplayer_lobby')->where('slots', '>', 0)->where('gameid', '=', $id)->execute()->as_array();

        if (count($data) != 1)
            return $graceful_fail;

        return (!$data[0]['password'] || $data[0]['password'] == hash('sha256', $pw, false));
    }

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
        if ($config['modes'][$mode]['type'] == 'multi_custom' && ($slots < $config['modes'][$mode]['slots'][0] || $slots > $config['modes'][$mode]['slots'][1]))
            return false;

        //Check speed
        if ($flow > 0 && (!in_array($flow,[15,30,60,120,300,600,900]) || $config['modes']['type'] != 'single'))
            return false;

        //Check name
        if ($config['modes'][$mode]['type'] == 'multi_custom' && (strlen($name) < 3 || strlen($name) > 96))
            return false;

        return max(1,(int)$config['jobs'][$job]['level']);
    }

    public function japi_check_pw() {
        $pw = $this->request->current()->post('password');
        $id = (int)$this->request->current()->post('id');
        $this->render(['proceed' => $this->check_password($id,$pw)]);
    }

    public function japi_start() {
        /**
         * @global Model_EUser $user
         */
        global $game, $player, $user;

        Error::i();
        $mode = (int)$this->request->current()->post('mode');
        $job = (int)$this->request->current()->post('job');
        $id = (int)$this->request->current()->post('id');
        $pw = $this->request->current()->post('password');
        $protect = $this->request->current()->post('protect');
        $flow = (int)$this->request->current()->post('flow');
        $name = preg_replace('/[^\w &.,!?\-\+:\/@\(\)=;\|]/', ' ', $this->request->current()->post('name'));
        $slots = (int)$this->request->current()->post('slots');

        if (!($level = $this->check_game_params($mode,$job,$flow,$slots,$id,$name)))
            return $this->error(\grge\E_STARTER_INVALID_SETUP);

        if ($id > 0) return $this->join_multiplayer($id,$job,$level,$pw);
        if ($name) return $this->start_multiplayer($mode,$job,$level,$name,'xx',$slots,$protect);

        return $this->start_singleplayer($mode,$flow,$job,$level);
    }

    public function action_lobby() {
        /** @global Model_Euser $user */
        global $user;

        $this->fill_multiplayer_lobby();

        $data = DB::select()->from('multiplayer_lobby')->where('slots', '>', 0);
        if ($user->lockouts_is_locked()) $data->where('password', 'IS NOT', NULL);
        $data = $data->order_by('password', 'ASC')->order_by('lang')->execute()->as_array();

        global $game;
        foreach ($data as &$entry) {
            foreach (['gameid','slots'] as $key)
                $entry[$key] = (int)$entry[$key];

            unset($entry['timestamp']);
            $entry['password'] = (bool)$entry['password'];

            $entry['locked'] = false;
            $local_game_obj = new Model_Game();
            if (!$local_game_obj->read($entry['gameid'], false)) {
                $entry['locked'] = true;
                continue;
            }
            $entry['mode'] = $local_game_obj->setting_mode();

            $entry['players'] = array();
            foreach ($local_game_obj->players(false) as $p) if ($p) {
                $entry['players'][] = array('name' => $p->name(), 'id' => (int)$p->id(), 'job' => $p->job(), 'cod' => $p->alive() ? null : $p->get_cod());
                if ($p->id() == $user->uid())
                    $entry['locked'] = true;
            } else $entry['locked'] = true;
        }
        $game = null;

        $this->add_widget(View::factory('pages/gameselect')
                ->set('database', Tool_Gamemodes::compile_mode_database(true))
                ->set('games', $data)
                ->set('languages', static::get_lang_flags())
                ->set('lock_count', $user->lockouts_get_count())
                ->set('lock_max', Kohana::$config->load('basic.multiplayer.mp_lockouts.max_count'))
                ->set('lock', $user->lockouts_is_locked())
                ->set('lock_timerange', $user->lockouts_get_time_range())
                ->render()
        );
        $this->render();

    }

}