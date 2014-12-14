<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Game extends Controller {

    protected static $force_login = true;
    protected static $menu = 'logout';

    private function render_location() {
        /** @global Model_Player $player */
        global $player;

        //ToDo: Radar effects
        list($radar_min, $radar_max, $radar_prop, $radar_increase) = $player->location()->zombie_factory()->get_radar_data();

        $this->add_data('debug', $player->location()->zombie_factory()->get_radar_data());

        $hideout = Tool_Scripts::current_location_hideout();
        $protected_hideout = $hideout && $hideout->get_defense() > 0;
        if ($protected_hideout)
            $radar_prop = 0;
        else $radar_prop = ($radar_prop > 0) ? ceil(pow($radar_prop,-1)) : 0;
        $radar_increase = ($radar_increase > 0) ? ceil(pow($radar_increase,-1)) : 0;


        $danger = ($radar_prop > 0) ? floor($radar_max/4) : 0;
        if (!$protected_hideout && $radar_prop <= 1.5)     $danger += 2;
        elseif (!$protected_hideout && $radar_prop <= 3)   $danger += 1;
        elseif ($radar_prop <= 15)  $danger -= 1;

        if ($radar_increase != 0 && $radar_increase <= 3)   $danger += 1;
        $danger = min(5,max(($radar_prop > 0) ? 1 : 0,$danger));

        $this->add_data('location', [
            'meta' => [
                'name' => __($player->location()->name()),
                'desc' => __($player->location()->description()),
                'outside' => $player->location()->is_outside()
            ],
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

    public function japi_data() {
        $this->render_location();
        $this->render(false);
    }

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
                $this->add_widget(View::factory('pages/noview')->render());

            elseif (Tool_Events::is_april_fools())
                // April fools
                //ToDo: Aprils Fools Page
                $this->add_widget(View::factory('pages/noview')->render());

            else
                // Ingame View
                $this->add_widget(View::factory('pages/ingame')->render());
        }
        //Show game summary
        else
            //ToDo: Death Page
            $this->add_widget(View::factory('pages/noview')->render());

        $this->render();
    }

}