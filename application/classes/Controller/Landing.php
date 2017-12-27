<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Landing extends Controller {

    private function handle_account_merging($key, $referrer) {
        // Get service
        $auth = false;
        foreach (Kohana::$config->load('mt.links') as $v)
            if (strpos($referrer,$v['url']) !== false)
                $auth = $v['auth'];;

        if (!$auth) return false;
        /** @var Model_Auth_Legacy $auth */

        // Check if user is connected via this service
        if ($auth::user_is_connected(Globals::CurrentUser()->uid())) {
            if ($auth::retrieve_user_id($key) != Globals::CurrentUser()->uid()) {

                $a = $this->session->as_array();
                foreach ($a as $k => $v)
                    if ($k != 'request') $this->session->delete($k);

                $this->redirect(URL::site('account/login',true));
                return true;
            } else return false;
        } else
            $this->redirect(URL::site('account/merge',true));

        return false;
    }

    public function action_redirect() {
        //Get redirect
        $rq = $this->session->get('request',["CLIENT_REQUEST" => []]);
        if (isset($rq["CLIENT_REQUEST"]['r']) && $rq["CLIENT_REQUEST"]['r']) {
            $url = $rq["CLIENT_REQUEST"]['r'];
            unset($rq["CLIENT_REQUEST"]['r']);
            $this->session->set('request',$rq);
            if ($url == 'dev/null') {
                $this->error(\grge\E_SERVER_INVALID_SESSION);
                $this->request->action('noaction');
                return;
            }
            if ($url != 'web/body') {
                $this->redirect(URL::site($url,true));
                return;
            }
        }

        //Check if user is logged in, and send him to login page if he is not
        if (!$this->session->get('user',NULL))
            $this->redirect(URL::site('account/login',true));

        if (isset($rq['HTTP_REFERER']) && isset($rq["CLIENT_REQUEST"]['key']) && $rq["CLIENT_REQUEST"]['key'])
            if ($this->handle_account_merging($rq["CLIENT_REQUEST"]['key'], $rq['HTTP_REFERER']))
                return;

        //Check if user has a game going on, and send him to game setup page if he is not
        if (!$this->session->get('game',NULL))
            $this->redirect(URL::site('lobby/main',true));

        //Redirect to game system
        $this->redirect(URL::site('game/redirect',true));
    }

}