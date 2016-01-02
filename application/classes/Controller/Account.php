<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Account extends Controller {

    /**
     * Login View
     * @throws Kohana_Exception
     */
    public function action_login() {
        if ($this->session->get('user',NULL)) {
            $this->redirect(URL::site('lobby/main', 'http'));
            return;
        }

        $rq = $this->session->get('request',["CLIENT_REQUEST" => []]);
        $key = isset($rq["CLIENT_REQUEST"]['key']) ? $rq["CLIENT_REQUEST"]['key'] : '';
        $ref = isset($rq["HTTP_REFERER"]) ? $rq["HTTP_REFERER"] : '';
        $pid = -1;

        $pre_service = '';
        $auth = '';

        // Read login services from config
        $services = [];
        foreach (Kohana::$config->load('mt.links') as $v) {
            $services[] = $v['name'];
            if ($key && $ref && strpos($ref,$v['url']) !== false) {
                $pre_service = $v['name'];
                $auth = $v['auth'];
            }
        }

        /** @var Model_Auth_Legacy $auth */
        if ($auth)
            $pid = $auth::retrieve_user_id($key);

        // Render page
        $this->add_widget(View::factory('pages/login')
            ->set('services', $services)
            ->set('preset_legacy_key', $key)
            ->set('preset_legacy_service', $pre_service)
            ->set('preset_zvid', $pid)
            ->render());

        // Render menu
        $this->add_menu('login');

        $this->render();
    }

    public function action_qr() {
        if ($this->session->get('user',NULL)) {
            $this->redirect(URL::site('lobby/main', 'http'));
            return;
        }

        $key = $this->request->param('key') ? trim($this->request->param('key')) : null;
        if (strlen($key) != 4) $key = null;

        // Render page
        $this->add_widget(View::factory('pages/qr')
            ->set('fill_key', $this->request->param('key'))
            ->render());

        // Render menu
        $this->add_menu('login');

        $this->render();
    }

    public function japi_mkqr() {
        /** @global Model_Euser $user */
        global $user;
        if (!$user) return;

        $pin = DB::select('pin')->from('qr')->where('uid','=',$user->uid())->where('timestamp', '>', time() - 300)->execute()->get('pin', false);

        while (!$pin) {
            $pin = '';
            for ($i = 0; $i < 4; $i++) {
                $c = mt_rand(0,61);
                if ($c >= 36) $c += 61;
                elseif ($c >= 10) $c += 55;
                else $c += 48;

                usleep(10000);
                $pin .= chr($c);
            }

            $chk = DB::select('pin')->from('qr')->where('pin','=',$pin)->where('timestamp', '>', time() - 604800)->execute()->get('pin', false);
            if ($chk) $pin = null;
            else {
                DB::delete('qr')->where('pin','=',$pin)->or_where('uid','=',$user->uid())->execute();
                DB::insert('qr', ['uid','pin','timestamp'])->values([$user->uid(),$pin,time()])->execute();
            }
        }

        $this->render(['pin' => $pin]);
    }

    public function action_settings() {
        // Render page
        $this->add_widget(View::factory('pages/settings')->set('url', URL::base(true))->render());

        // Render menu
        $this->add_menu('logout');

        $this->render();
    }

    public function japi_mentorize() {
        /** @global Model_Euser $user */
        global $user;
        if (!$user) return $this->render(['success' => 0]);



        $uid = $this->request->current()->post('uid');
        if (!$uid && $this->request->current()->post('mrk'))
            $uid = Model_Euser::get_uid_from_mentoring_ref($this->request->current()->post('mrk'));

        if ($uid == -1)
            return $this->render([
                'success' => (int)$user->set_mentor_id(-1)
            ]);


        if (!$uid || !Model_Euser::check_mentor($user->uid(), $uid)) return $this->render(['success' => 0, 'a' => $uid]);
        else return $this->render([
            'success' => (int)$user->set_mentor_id($uid)
        ]);
    }

    public function japi_cashout() {
        /** @global Model_Euser $user */
        global $user;
        if (!$user) return $this->render(['success' => 0]);

        $cash = Model_Euser::get_mentor_braincoins($user->uid(), null, false);
        if ($cash && Model_Euser::reset_mentor_braincoins($user->uid(), null)) {
            Model_Euser::award_coins($user->uid(), $cash);
            return $this->render(['success' => 1]);
        } else return $this->render(['success' => 0]);
    }

    public function japi_qr() {
        sleep(5);
        $pin = trim($this->request->current()->post('key'));

        if (!$pin || strlen($pin) != 4)
            return $this->error(\grge\E_AUTH_INCOMPLETE_REQUEST);

        $uid = DB::select('uid')->from('qr')->where('pin','=',$pin)->where('timestamp', '>', time() - 300)->execute()->get('uid', 0);

        if ($uid) {
            DB::delete('qr')->where('pin','=',$pin)->or_where('uid','=',$uid)->execute();
            return $this->japi_login($uid);
        }

        else return $this->error(\grge\E_AUTH_INVALID_KEY);
    }

    public function japi_remove_tokens() {
        /** @global Model_Euser $user */
        global $user;
        if (!$user) return false;

        Model_Auth_Token::user_unlink($user->uid());

        return $this->japi_logout();
    }

    /**
     * Merge View
     * @throws Kohana_Exception
     */
    public function action_merge() {
        global $user;

        if (!$this->session->get('user',NULL)) {
            $this->redirect(URL::site('account/login', 'http'));
            return;
        }

        $rq = $this->session->get('request',["CLIENT_REQUEST" => []]);
        $key = isset($rq["CLIENT_REQUEST"]['key']) ? $rq["CLIENT_REQUEST"]['key'] : '';
        $ref = isset($rq["HTTP_REFERER"]) ? $rq["HTTP_REFERER"] : '';
        $pid = -1;

        $pre_service = '';
        $auth = '';

        // Read login services from config
        $services = [];
        foreach (Kohana::$config->load('mt.links') as $v) {
            $services[] = $v['name'];
            if ($key && $ref && strpos($ref,$v['url']) !== false) {
                $pre_service = $v['name'];
                $auth = $v['auth'];
            }
        }

        /** @var Model_Auth_Legacy $auth */
        if ($auth)
            $pid = $auth::retrieve_user_id($key);
        else $this->redirect(URL::site('lobby/main', 'http'));
        /** @var Model_Auth_Legacy $authenticator */

        $authenticator = new $auth($key);
        $connected = !$authenticator->getLastError();

        if ($pid > 0)
            $allow = !Model_Euser::get_soulpoints($pid);
        else $allow = true;

        // Render page
        $this->add_widget(View::factory('pages/merge')
            ->set('username',$connected ? $authenticator->getRemoteName() : null)
            ->set('pid',$pid)
            ->set('allow', $allow)
            ->set('key',$key)
            ->set('service',$pre_service)
            ->render());

        // Render menu
        $this->add_menu('logout');

        $this->render();
    }

    /**
     * Merge View
     * @throws Kohana_Exception
     */
    public function japi_merge() {
        /** @global Model_Euser $user */
        global $user;

        if (!$user) return $this->render();

        $key = $this->request->post('key');
        $service = $this->request->post('service');

        if (!$key || !$service || !($cfg = Kohana::$config->load('mt.links.' . $service))) return $this->render();

        /** @var Model_Auth_Legacy $authenticator */
        $authenticator = new $cfg['auth']($key);
        $connected = !$authenticator->getLastError();

        if (!$connected) return $this->render(['success' => 0]);

        $pid = $authenticator::retrieve_user_id($key);
        if ($pid > 0)
            $allow = !Model_Euser::get_soulpoints($pid);
        else $allow = true;

        if ($allow) {
            $authenticator::user_unlink_all($pid);
            $authenticator->connectToLocal($user->uid());
        }

        return $this->render(['success' => $allow ? 1 : 0]);
    }

    /**
     * License View
     * @throws Kohana_Exception
     */
    public function action_license() {
        $this->add_widget(View::factory('pages/license')
            ->set('data', array_filter((array)Kohana::$config->load('licenses'), function($entry) {
                if (!isset($entry['skin'])) return true;

                if (is_array($entry['skin']) && !in_array(Tool_Events::current_skin(), $entry['skin'])) return false;
                if (is_string($entry['skin']) && $entry['skin'] != Tool_Events::current_skin()) return false;

                return true;
            }))
            ->render());
        $this->render();
    }

    /**
     * Login API
     * @return bool
     * @throws Kohana_Exception
     */
    public function japi_login($uid = null) {

        $nw = 1;
        $skip = (int)$this->request->current()->post('skip');

        if ($uid == null) {
            //Get key
            $key = $this->request->current()->post('key');
            $host = $this->request->current()->post('service');

            if (!$key || !$host)
                return $this->error(\grge\E_AUTH_INCOMPLETE_REQUEST);

            $authenticator = null;
            switch ($host) {
                case 'Die Verdammten':
                    $authenticator = new Model_Auth_Hordesde($key, true);
                    break;
                case 'Die2Nite':
                    $authenticator = new Model_Auth_Hordesen($key, true);
                    break;
                case 'Token':
                    $authenticator = new Model_Auth_Token($key);
                    break;
                default: return $this->error(grge\E_AUTH_INVALID_PROVIDER);
            }

            if (!($nw = $authenticator->connectToLocal()) || !$authenticator->is_ready())
                switch ($authenticator->getLastError()) {
                    case 'invalid_host': return $this->error(\grge\E_AUTH_INVALID_PROVIDER);
                    case 'connection_failed': return $this->error(\grge\E_AUTH_CONNECTION_FAILED);
                    case 'protocol_failed': return $this->error(\grge\E_AUTH_CONNECTION_FAILED);
                    case 'supplement_failed': return $this->error(\grge\E_AUTH_INVALID_CRED);
                    case 'invalid_keys': return $this->error(\grge\E_AUTH_INVALID_KEY);
                    default: return $this->error(\grge\E_AUTH_DOWNTIME, ['mt_error_passthrough' => $authenticator->getLastError()]);
                }

            $uid = $authenticator->getLocalID();
        } else
            $authenticator =  $authenticator = new Model_Auth_Token(Model_Auth_Token::token($uid));


        //Get whitelisting entry
        $wl = DB::select('relation')->from('user_flags')->where('user','=',$uid)->and_where('relation','IN',['ALLOW','DENY'])->and_where('data','=','WHITELIST')->execute()->as_array();
        if (count($wl) > 0) $wl = $wl[0]['relation'];
        else $wl = false;

        //Check if user is banned
        if ($wl == 'DENY')
            return $this->error(\grge\E_AUTH_ACCOUNT_BANNED);
        else if (Kohana::$environment != Kohana::DEVELOPMENT && Kohana::$config->load('basic.access.whitelisting') && $wl != 'ALLOW')
            return $this->error(\grge\E_AUTH_WHITELISTING_FAILED);

        //Create user object and try to read from database
        $user = new Model_Euser($this->session->id());
        if ($user->read($uid))
        {
            //Append user object to session
            $this->session->set('user',$user);

            //Try to load current game
            if ($gameid = $user->get_current_game())
            {
                //Create game object and try to read from database
                $game = new Model_Game;
                if (!$game->read($gameid)) return $this->error(\grge\E_GAME_INDEX_ERROR);

                //Append game object to session
                try {
                    $this->session->set('game',$game);
                } catch (Exception $e) {
                    Session::instance()->destroy();
                    return $this->error(\grge\E_GAME_INDEX_ERROR);
                }
            }

            $this->render([
                'redirect' => $nw === 2 ? 'lobby/newuser' : ($skip && $user->get_current_game() ? 'game/redirect' : 'lobby/main'),
                'login' => [
                    'user' => $user->uid(),
                    'name' => $user->name(),
                    'avatar' => $authenticator->getRemoteAvatarUrl(),
                    'token' => Model_Auth_Token::token($user->uid())
                    ]
                ]);
        }
        else return $this->error(\grge\E_AUTH_PROFILE_DAMAGED);

        return true;
    }

    /**
     * Logout API
     */
    public function japi_logout() {
        $this->session->destroy();
        $this->render([
            'redirect' => 'account/login',
        ]);
    }
}