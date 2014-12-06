<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Gamemaster extends Controller {

    public function before() {
        /** @global Model_User $user */
        global $user;

        parent::before();

        //Init error class
        Error::i();

        //Check if user already has a running game
        if ($user->get_current_game()) {
            $this->error(\grge\E_STARTER_GAME_RUNNING);
            $this->request->action('noaction');
        }
    }

    private function start_singleplayer() {
        $this->add_note('info','Single Player Start');
        $this->render();
        return true;
    }

    private function start_multiplayer() {
        $this->add_note('info','Multi Player Start');
        $this->render();
        return true;
    }

    private function join_multiplayer($id,$job,$pw) {
        if (!$this->check_password($id,$pw,false))
            return $this->error(\grge\E_STARTER_INVALID_SETUP);

        $this->render();
        return true;
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

        return true;
    }

    public function japi_check_pw() {
        $pw = $this->request->current()->post('password');
        $id = (int)$this->request->current()->post('id');
        $this->render(['proceed' => $this->check_password($id,$pw)]);
    }

    public function japi_start() {
        Error::i();

        $mode = (int)$this->request->current()->post('mode');
        $job = (int)$this->request->current()->post('job');
        $id = (int)$this->request->current()->post('id');
        $pw = $this->request->current()->post('password');
        $protect = $this->request->current()->post('protect');
        $flow = (int)$this->request->current()->post('flow');
        $name = preg_replace('/[^\w &.,!?\-\+:\/@\(\)=;\|]/', ' ', $this->request->current()->post('name'));
        $slots = (int)$this->request->current()->post('slots');

        if (!$this->check_game_params($mode,$job,$flow,$slots,$id,$name))
            return $this->error(\grge\E_STARTER_INVALID_SETUP);

        if ($id > 0) return $this->join_multiplayer($id,$job,$pw);
        if ($name) return $this->start_multiplayer();

        return $this->start_singleplayer();
    }

    public function action_lobby() {
        /** @global Model_Euser $user */
        global $user;

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
                ->set('lock_count', $user->lockouts_get_count())
                ->set('lock_max', Kohana::$config->load('basic.multiplayer.mp_lockouts.max_count'))
                ->set('lock', $user->lockouts_is_locked())
                ->set('lock_timerange', $user->lockouts_get_time_range())
                ->render()
        );
        $this->render();

    }

}