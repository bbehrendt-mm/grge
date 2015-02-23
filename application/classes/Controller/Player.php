<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Player extends Controller_Game {

    public function japi_reflux() {
        /**
         * @global $game Model_Game
         * @global $player Model_Player
         * @var $item Model_Items_Abstract_Item
         */
        global $game, $player;

        $to = (int)$this->request->post('set');
        if ($to < 0 || $to > 6 || $player->vote_time() == $to) return false;

        if (!$player->vote_time($to, (int)Kohana::$config->load('balancing.pause.min_interval')))
            $player->log()->add('Deine Sperrzeit ist noch nicht abgelaufen.');
        elseif (count($game->players(true)) == 1 && $game->duration() == 0) {
            $game->recalculate_flow();
            $player->log()->add('Du hast die Spielgeschwindigkeit geändert.');
        } else $player->log()->add('Die Spielgeschwindkeit wird nach Ablauf des aktuellen Ticks angepasst.');

        return $this->japi_data();
    }

}