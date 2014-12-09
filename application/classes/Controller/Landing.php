<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Landing extends Controller {

    public function action_redirect() {
        //Check if user is logged in, and send him to login page if he is not
        if (!$this->session->get('user',NULL))
            $this->redirect(URL::site('account/login', 'http'));

        //Check if user has a game going on, and send him to game setup page if he is not
        if (!$this->session->get('game',NULL))
            $this->redirect(URL::site('lobby/main', 'http'));

        //Redirect to game system
        $this->redirect(URL::site('game/redirect', 'http'));
    }

}