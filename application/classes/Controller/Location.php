<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Location extends Controller_Game {

    protected static $force_login = true;
    protected static $menu = 'logout';

    private function siege($fight) {
        /**
         * @global $player Model_Player
         */
        global $player;

        if ($player->buff_retr('passout') || $player->buff_retr('fragile') || $player->can_escape()) return;

        if ($player->location()->zombie_pop() > 0)
            $player->location()->break_out(true);

        $this->japi_data();
    }

    public function japi_fight() {
        $this->siege(true);
    }

    public function japi_flee() {
        $this->siege(false);
    }
}