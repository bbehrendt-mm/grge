<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Player extends Controller_Game {

    public static function battle_ai_ammo_types() {
        return ['Model_Items_Battery','Model_Items_Ammo','Model_Items_Splinter','Model_Items_Bolts'];
    }

    public function japi_reflux() {
        /**
         * @global $game Model_Game
         * @global $player Model_Player
         * @var $item Model_Items_Abstract_Item
         */
        global $game, $player;
        if ($game->timeflow() != 1) return false;

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

    public function japi_pause() {
        /**
         * @global $game Model_Game
         * @global $player Model_Player
         * @var $item Model_Items_Abstract_Item
         */
        global $game, $player;
        if ($game->timeflow() != 0) return false;

        $set = (int)$this->request->post('set');

        if ($set) {
            if ($game->paused()) return false;
            if ($game->pauselock() > (time() - Kohana::$config->load('balancing.pause.min_interval'))) {
                $player->log()->add('Deine Sperrzeit ist noch nicht abgelaufen.');
                return $this->japi_data();
            } else {
                $game->pause();
                return $this->render(['redirect' => 'game/redirect']);
            }
        } else {
            if (!$game->paused()) return false;
            if ($game->pauselock() > (time() - Kohana::$config->load('balancing.pause.min_duration'))) {
                $this->add_note('error', __('Deine Sperrzeit ist noch nicht abgelaufen.'));
                return $this->render();
            } else {
                $game->unpause();
                return $this->render(['redirect' => 'game/redirect']);
            }
        }
    }

    public function japi_ai() {
        /**
         * @global $player Model_Player
         * @var $item Model_Items_Abstract_Item
         */
        global $player;

        $lock_energy = (int)$this->request->post('wp_energy');
        $lock_throw = (int)$this->request->post('wp_throw');
        $lock_tank = (int)$this->request->post('wp_tank');
        $aitype = (int)$this->request->post('ai');

        if ($aitype < 1 || $aitype > 3) return false;

        $ammo_data = [];
        $wp_ammo = $this->request->post('wp_ammo');

        if (!is_array($wp_ammo)) return false;

        foreach (Controller_Player::battle_ai_ammo_types() as $key => $ammo) {
            /** @var Model_Items_Abstract_Ammo|string $ammo */
            if ((int)$wp_ammo[$key]) $ammo_data[] = $ammo;
        };

        //var_dump($lock_energy, $lock_throw, $lock_tank); die;

        $player->set_battle_settings((bool)$lock_energy, (bool)$lock_tank, (bool)$lock_throw, $aitype, $ammo_data);
        $player->log()->add('Du hast dein Kampfverhalten angepasst.');
        return $this->japi_data();
    }
}