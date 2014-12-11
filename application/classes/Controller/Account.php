<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Account extends Controller {

    public function action_login() {

        $services = [];
        foreach (Kohana::$config->load('mt.links') as $v)
            $services[] = $v['name'];

        $this->add_widget(View::factory('pages/login')
            ->set('services', $services)
            ->render());

        $this->add_widget('main-menu', View::factory('menus/login')->render());

        $this->render();
    }

    public function japi_login($attempt_local = false) {
        //Get key
        $key = $this->request->current()->post('key');
        $host = $this->request->current()->post('service');

        if (!$key || !$host)
            return $this->error(\grge\E_AUTH_INCOMPLETE_REQUEST);

        if (!Kohana::$config->load('mt.links.' . $host))
            return $this->error(\grge\E_AUTH_INVALID_PROVIDER);

        if (!Kohana::$config->load('mt.links.' . $host . '.token') && !$attempt_local)
            return $this->japi_login(true);

        if ($attempt_local) {
            if (!$data = Model_Euser::fromfs($key, Kohana::$config->load('mt.links.' . $host . '.lang')))
                return $this->error(\grge\E_AUTH_LOCAL_PROVIDER_FAILED);

            $mtid = $data['mtid'];
            $region = $data['origin'];
            $name = $data['name'];
            $avatar = null;

        } else {
            $sk = Kohana::$config->load('mt.links.' . $host . '.token');
            $url = 'http://' . Kohana::$config->load('mt.links.' . $host . '.url') . "/xml/?k={$key};sk={$sk}";

            //Load Data from remote server and store DOM
            $xml = new DOMDocument( );
            try {
                if (!$xml->loadXML(file_get_contents($url)))
                    return $this->error(\grge\E_AUTH_CONNECTION_FAILED);
            } catch (Exception $e) {
                return $this->error(\grge\E_AUTH_CONNECTION_FAILED);
            }

            //Generate XPath object
            $xpath = new DOMXPath($xml);

            //Let's check for errors first
            if ($error = $xpath->evaluate('string(//error/@code)')) if ($error != 'not_in_game')
                switch ($error) {
                    case 'invalid_keys': return $this->error(\grge\E_AUTH_INVALID_KEY);
                    default: return $this->error(\grge\E_AUTH_DOWNTIME, ['mt_error_passthrough' => $error]);
                }

            //Get MT ID and region (language) plus name
            if (!($mtid = (int)$xpath->evaluate('string(//owner/citizen/@id)')) || !($region = $xpath->evaluate('string(//headers/@language)')) || !($name = $xpath->evaluate('string(//owner/citizen/@name)')))
                return $this->error(\grge\E_AUTH_INVALID_CRED);

            $avatar = $xpath->evaluate('string(//owner/citizen/@avatar)');
        }

        //Finally get a real UID from all the crap we just collected
        if (!$uid = Model_Euser::mt2gr($mtid, $region))
            //Register player, if he does not yet have an account
            $uid = Model_Euser::register($mtid, $region, $name);

        //Get whitelisting entry
        $wl = DB::select('relation')->from('user_flags')->where('user','=',$uid)->and_where('relation','IN',['ALLOW','DENY'])->and_where('data','=','WHITELIST')->execute()->as_array();
        if (count($wl) > 0) $wl = $wl[0]['relation'];
        else $wl = false;

        //Check if user is banned
        if ($wl == 'DENY')
            return $this->error(\grge\E_AUTH_ACCOUNT_BANNED);
        else if (Kohana::$config->load('basic.access.whitelisting') && $wl != 'ALLOW')
            return $this->error(\grge\E_AUTH_WHITELISTING_FAILED);

        //Create user object and try to read from database
        $user = new Model_Euser($this->session->id());
        if ($user->read($uid))
        {
            //Append user object to session
            $this->session->set('user',$user);

            //Create or update FS
            Model_Euser::tofs($key, $user->name(), $mtid, $region);

            //Try to load current game
            if ($gameid = $user->get_current_game())
            {
                //Create game object and try to read from database
                $game = new Model_Game;
                if (!$game->read($gameid)) throw new Exception('Unable to bind game object; Invalid database reference.');

                //Append game object to session
                try {
                    $this->session->set('game',$game);
                } catch (Exception $e) {
                    Session::instance()->destroy();
                    throw $e;
                }
            }

            $this->render([
                'redirect' => 'lobby/main',
                'login' => [
                    'user' => $user->uid(),
                    'name' => $user->name(),
                    'region' => $region,
                    'mtid' => $mtid,
                    'avatar' => $avatar,
                    'host' => $host,
                    'key' => $key
                    ]
                ]);
        }
        else return $this->error(\grge\E_AUTH_PROFILE_DAMAGED);

        return true;
    }

    public function japi_logout() {
        $this->session->destroy();
        $this->render([
            'redirect' => 'account/login',
        ]);
    }
}