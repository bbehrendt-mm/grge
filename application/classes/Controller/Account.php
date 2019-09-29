<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Account extends Controller {

    /**
     * Login View
     * @throws Kohana_Exception
     */
    public function action_login(): void
    {
        if ($this->session->get('user',NULL)) {
            self::redirect(URL::site('lobby/main',true));
            return;
        }

        $rq = $this->session->get('request',['CLIENT_REQUEST' => []]);
        $key = $rq['CLIENT_REQUEST']['key'] ?? '';
        $ref = $rq['CLIENT_REQUEST']['ref'] ??
            $rq['HTTP_REFERER'] ?? '';
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

    public function action_qr(): void
    {
        if ($this->session->get('user',NULL)) {
            self::redirect(URL::site('lobby/main',true));
            return;
        }

        $key = $this->request->param('key') ? trim($this->request->param('key')) : null;
        if (strlen($key) !== 4) $key = null;

        // Render page
        $this->add_widget(View::factory('pages/qr')
            ->set('fill_key', $this->request->param('key'))
            ->render());

        // Render menu
        $this->add_menu('login');

        $this->render();
    }

    public function japi_mkqr(): void
    {
        if (!Globals::hasCurrentUser()) return;

        $pin = DB::select('pin')->from('qr')->where('uid','=',Globals::CurrentUserF()->uid())->where('timestamp', '>', time() - 300)->execute()->get('pin', false);

        while (!$pin) {
            $pin = '';
            for ($i = 0; $i < 4; $i++) {
                $c = random_int(0,61);
                if ($c >= 36) $c += 61;
                elseif ($c >= 10) $c += 55;
                else $c += 48;

                usleep(10000);
                $pin .= chr($c);
            }

            $chk = DB::select('pin')->from('qr')->where('pin','=',$pin)->where('timestamp', '>', time() - 604800)->execute()->get('pin', false);
            if ($chk) $pin = null;
            else {
                DB::delete('qr')->where('pin','=',$pin)->or_where('uid','=',Globals::CurrentUserF()->uid())->execute();
                DB::insert('qr', ['uid','pin','timestamp'])->values([Globals::CurrentUserF()->uid(),$pin,time()])->execute();
            }
        }

        $this->render(['pin' => $pin]);
    }

    public function japi_remove_avatar(): void
    {
        Globals::CurrentUserF()->remove_avatar( );
        $this->render([
            'success' => true,
            'id' => Globals::CurrentUserF()->uid()
        ]);
        return;
    }

    public function japi_cancel_pw(): void {
        if (!Model_Auth_Password::user_is_connected( Globals::CurrentUserF()->uid() )) {
            $this->render(['success' => false]);
            return;
        }

        Model_Auth_Password::user_unlink( Globals::CurrentUserF()->uid() );
        $this->render(['success' => true]);
    }

    public function japi_make_pw(): void
    {
        $id = Globals::CurrentUserF()->uid();
        $mail = Request::current()->post('email') ?: null;
        $pass = Request::current()->post('pass') ?: null;

        if (strlen( $pass ) < 5) {
            $this->render(['success' => false, 'error' => 'pass']);
            return;
        }
        if (!Model_Auth_Password::check_email_validity( $mail )) {
            $this->render(['success' => false, 'error' => 'email']);
            return;
        }

        if (!Model_Auth_Password::create( $id, $mail, $pass )) {
            $this->render(['success' => false]);
            return;
        }

        $ret = mail(
            $mail,
            'ZombVival - ' . __('E-Mail Adresse bestätigen'),
            View::factory('mail/passkey')->set('username', Globals::CurrentUserF()->name())->set('key', Model_Auth_Password::user_activation_key( $id )),
            [
                'MIME-Version' => '1.0',
                'Content-type' => 'text/html; charset=UTF-8',
                'From' => __('Zombie-Briefträger') . ' <mailzombie@' . $_SERVER['SERVER_NAME'] . '>'
            ]
        );

        $this->render(['success' => $ret]);
    }

    public function japi_activate_pw(): void
    {
        $id = Globals::CurrentUserF()->uid();
        $token = Request::current()->post('t') ?: null;

        if (!Model_Auth_Password::user_pending_activation( $id )) {
            $this->render(['success' => false]);
            return;
        }

        if ($token !== Model_Auth_Password::user_activation_key( $id )) {
            $this->render(['success' => false]);
            return;
        }

        $this->render(['success' => Model_Auth_Password::user_activate( $id )]);
    }

    public function japi_upload_avatar():void {

        if (!extension_loaded('imagick')){
            $this->render(['success' => false]);
            return;
        }

        $payload = base64_decode(Request::current()->post('data'), true) ?: null;
        if (strlen( $payload ) > 3145728) {
            $this->error(\grge\E_IMAGE_TOO_LARGE);
            return;
        }

        if (!$payload) {
            $this->render(['success' => false]);
            return;
        }

        $im_image = new Imagick();
        $proessed_image_data = null;

        try {
            if (!$im_image->readImageBlob($payload)) {
                $this->error(\grge\E_IMAGE_CORRUPTED);
                return;
            }

            if (!in_array($im_image->getImageFormat(), ['GIF','JPEG','BMP','PNG','WEBP'])) {
                $this->error(\grge\E_IMAGE_FORMAT_UNSUPPORTED);
                return;
            }

            $w = $im_image->getImageWidth();
            $h = $im_image->getImageHeight();

            if ($w / $h < 0.1 || $h / $w < 0.1 || $h < 16 || $w < 16) {
                $this->error(\grge\E_IMAGE_RESOLUTION_REJECTED);
                return;
            }

            if ( max($w,$h) > 200 && !$im_image->resizeImage( min(200,max(100,$w,$h)), min(200,max(100,$w,$h)), imagick::FILTER_SINC, 0, true )){
                $this->error(\grge\E_IMAGE_TRANSFORMATION_FAILURE);
                return;
            }

            $im_image->setImageBackgroundColor('black');
            $im_image->setImageAlphaChannel(Imagick::ALPHACHANNEL_REMOVE);
            if ($im_image->getImageFormat() !== "GIF")
                $im_image = $im_image->mergeImageLayers(Imagick::LAYERMETHOD_FLATTEN);

            switch ($im_image->getImageFormat()) {
                case 'JPEG':
                    $im_image->setImageCompressionQuality ( 90 );
                    break;
                case 'PNG':
                    $im_image->setOption('png:compression-level', 9);
                    break;
                case 'GIF':
                    $im_image->setOption('optimize', true);
                    break;
                default: break;
            }

            $proessed_image_data = $im_image->getImagesBlob();
            if (strlen($proessed_image_data) > 1048576){
                $this->error(\grge\E_IMAGE_OUTPUT_TOO_LARGE);
                return;
            }
        } catch (Exception $e) {
            $this->error(\grge\E_IMAGE_PIPELINE_FAILURE);
            return;
        }

        Globals::CurrentUserF()->update_avatar_override( $proessed_image_data, strtolower( $im_image->getImageFormat() ) );
        $this->render(['success' => true, 'access' => Globals::CurrentUserF()->get_access_code()]);
    }

    public function japi_sync_mt(): void
    {
        // Account Providers
        $accounts = Model_Auth_Legacy::get_all_providers( Globals::CurrentUserF()->uid() );

        $authenticator = null;
        if ( isset( $accounts['Model_Auth_Hordesde'] ) ) {
            $authenticator = new Model_Auth_Hordesde($accounts['Model_Auth_Hordesde']['var1'], false);
            if (!$authenticator->connectToLocal() || !$authenticator->is_ready()) $authenticator = null;
        }
        if ( !$authenticator && isset( $accounts['Model_Auth_Hordesde'] ) ) {
            $authenticator = new Model_Auth_Hordesen($accounts['Model_Auth_Hordesen']['var1'], false);
            if (!$authenticator->connectToLocal() || !$authenticator->is_ready()) $authenticator = null;
        }

        if (!$authenticator) {
            $this->render(['success' => false]);
            return;
        }

        $hash = md5( $authenticator->getRemoteName() . '/' . $authenticator->getRemoteAvatarUrl());
        $control = $this->request->post('control', null);

        if ($control !== null) {

            if ($control !== $hash) {
                $this->render(['success' => false]);
                return;
            }

            Globals::CurrentUserF()->update_avatar_override( null, null);

            if ($authenticator->getRemoteName() === Globals::CurrentUserF()->name( true ) ) {
                Globals::CurrentUserF()->update_name_override( $authenticator->getRemoteName() );
                Globals::CurrentUserF()->update( null, $authenticator->getRemoteAvatarUrl() );
            } else Globals::CurrentUserF()->update( $authenticator->getRemoteName(), $authenticator->getRemoteAvatarUrl() );
        }

        $this->render([
            'success' => true,
            'control' => $hash,
            'id' => Globals::CurrentUserF()->uid(),
            'avatar' => [
                'old' => Model_Euser::avatar_by_id(Globals::CurrentUserF()->uid(), false),
                'new' => $authenticator->getRemoteAvatarUrl(),
            ],
            'name' => [
                'old' => Globals::CurrentUserF()->name(),
                'new' => $authenticator->getRemoteName()
            ],
        ]);
    }

    public function action_settings(): void
    {
        // Account Providers
        $accounts = Model_Auth_Legacy::get_all_providers( Globals::CurrentUserF()->uid() );

        if (Model_Auth_Password::user_is_activated( Globals::CurrentUserF()->uid() )) $pw_stage = 2;
        else if (Model_Auth_Password::user_pending_activation( Globals::CurrentUserF()->uid() )) $pw_stage = 1;
        else $pw_stage = 0;

        // Render page
        $this->add_widget(
            View::factory('pages/settings')
                ->set('mail', Model_Auth_Password::user_getEmail( Globals::CurrentUserF()->uid() ))
                ->set('pw_stage', $pw_stage)
                ->set('url', URL::base(true))
                ->set('avatar', Model_Euser::avatar_by_id(Globals::CurrentUserF()->uid(), false))
                ->set('user',   Globals::CurrentUserF()->name())
                ->set('user_c',   Globals::CurrentUserF()->name(true))
                ->set('fake',   Globals::CurrentUserF()->fakename())
                ->set('mentor', Model_Euser::get_mentoring_ref(Globals::CurrentUserF()->uid()))
                ->set('id', Globals::CurrentUserF()->uid())
                ->set('dv_id',  isset( $accounts['Model_Auth_Hordesde'] ) ? (int)$accounts['Model_Auth_Hordesde']['rid'] : -1)
                ->set('d2n_id', isset( $accounts['Model_Auth_Hordesen'] ) ? (int)$accounts['Model_Auth_Hordesen']['rid'] : -1)
                ->set('token',  isset( $accounts['Model_Auth_Token'] )    ? true : false)
                ->set('local_login',  false)
                ->render()
        );

        // Render menu
        $this->add_menu('logout');

        $this->render();
    }

    public function japi_mentorize(): bool {
        if (!Globals::hasCurrentUser()) return $this->render(['success' => 0]);

        $uid = Request::current()->post('uid');
        if (!$uid && Request::current()->post('mrk'))
            $uid = Model_Euser::get_uid_from_mentoring_ref(Request::current()->post('mrk'));

        if ($uid === -1)
            return $this->render([
                'success' => (int)Globals::CurrentUserF()->set_mentor_id(-1)
            ]);


        if (!$uid || !Model_Euser::check_mentor(Globals::CurrentUserF()->uid(), $uid)) return $this->render(['success' => 0, 'a' => $uid]);
        else return $this->render([
            'success' => (int)Globals::CurrentUserF()->set_mentor_id($uid)
        ]);
    }

    public function japi_cashout(): bool {
        if (!Globals::hasCurrentUser()) return $this->render(['success' => 0]);

        $cash = Model_Euser::get_mentor_braincoins(Globals::CurrentUserF()->uid(), null, false);
        if ($cash && Model_Euser::reset_mentor_braincoins(Globals::CurrentUserF()->uid(), null)) {
            Model_Euser::award_coins(Globals::CurrentUserF()->uid(), $cash);
            return $this->render(['success' => 1]);
        } else return $this->render(['success' => 0]);
    }

    public function japi_qr(): bool {
        sleep(5);
        $pin = trim(Request::current()->post('key'));

        if (!$pin || strlen($pin) !== 4)
            return $this->error(\grge\E_AUTH_INCOMPLETE_REQUEST);

        $uid = DB::select('uid')->from('qr')->where('pin','=',$pin)->where('timestamp', '>', time() - 300)->execute()->get('uid', 0);

        if ($uid) {
            DB::delete('qr')->where('pin','=',$pin)->or_where('uid','=',$uid)->execute();
            return $this->japi_login($uid);
        }

        else return $this->error(\grge\E_AUTH_INVALID_KEY);
    }

    public function japi_remove_tokens(): void {
        if (!Globals::hasCurrentUser()) return;
        Model_Auth_Token::user_unlink(Globals::CurrentUserF()->uid());
        $this->japi_logout();
    }

    /**
     * Merge View
     * @throws Kohana_Exception
     */
    public function action_merge(): void
    {
        if (!$this->session->get('user',NULL)) {
            self::redirect(URL::site('account/login',true));
            return;
        }

        $rq = $this->session->get('request',['CLIENT_REQUEST' => []]);
        $key = $rq['CLIENT_REQUEST']['key'] ?? '';
        $ref = $rq['HTTP_REFERER'] ?? '';
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
        else self::redirect(URL::site('lobby/main',true));
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
    public function japi_merge(): bool {
        if (!Globals::hasCurrentUser()) return $this->render();

        $key = self::post('key');
        $service = self::post('service');

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
            $authenticator->connectToLocal(Globals::CurrentUserF()->uid());
        }

        return $this->render(['success' => $allow ? 1 : 0]);
    }

    /**
     * License View
     * @throws Kohana_Exception
     */
    public function action_license(): void
    {
        $this->add_widget(View::factory('pages/license')
            ->set('data', array_filter((array)Kohana::$config->load('licenses'), function($entry) {
                if (!isset($entry['skin'])) return true;

                if (is_array($entry['skin']) && !in_array(
                        Tool_Events::current_skin(), $entry['skin'], true
                    )
                ) return false;
                if (is_string($entry['skin']) && $entry['skin'] !== Tool_Events::current_skin()) return false;

                return true;
            }))
            ->render());
        $this->render();
    }

    /**
     * Login API
     *
     * @param null $uid
     *
     * @return bool
     * @throws Kohana_Exception
     */
    public function japi_login($uid = null): bool {

        sleep(3);

        $nw = 1;
        $skip = (int)Request::current()->post('skip');

        if ($uid === null) {
            //Get key
            $mail = Request::current()->post('user');
            $key  = Request::current()->post('key');
            $host = Request::current()->post('service');

            if (!$key || !$host)
                return $this->error(\grge\E_AUTH_INCOMPLETE_REQUEST);

            $authenticator = null;
            switch ($host) {
                case 'grge':
                    $authenticator = new Model_Auth_Password($mail, $key);
                    break;
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
            $authenticator = new Model_Auth_Token(Model_Auth_Token::token($uid));

        Model_Euser::create_profile_data_if_missing( $uid );

        //Get whitelisting entry
        $wl = DB::select('relation')->from('user_flags')->where('user','=',$uid)->and_where('relation','IN',['ALLOW','DENY'])->and_where('data','=','WHITELIST')->execute()->as_array();
        if (count($wl) > 0) $wl = $wl[0]['relation'];
        else $wl = false;

        //Check if user is banned
        if ($wl === 'DENY')
            return $this->error(\grge\E_AUTH_ACCOUNT_BANNED);
        else if ($wl !== 'ALLOW' && Kohana::$environment !== Kohana::DEVELOPMENT && Kohana::$config->load('basic.access.whitelisting'))
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
    public function japi_logout(): void
    {
        $this->session->destroy();
        $this->render([
            'redirect' => 'account/login',
        ]);
    }
}