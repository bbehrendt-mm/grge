<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Game extends Controller {

    protected static $force_login = true;
    protected static $menu = 'logout';

    public function action_redirect() {
        /**
         * @global Model_Game $game
         * @global Model_Player $player
         */
        global $game, $player;

        //Redirect
        if (!$game)
            $this->redirect(URL::site('landing/redirect', 'http'));
        if (!$player) {
            $this->session->delete('game');
            $this->redirect(URL::site('landing/redirect', 'http'));
        }

        $this->add_widget(View::factory('pages/noview')->render());
        $this->render();
    }

}