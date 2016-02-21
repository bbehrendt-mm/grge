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

        $to = (int)$this->post('set');
        if ($to < 0 || $to > 6 || $player->vote_time() == $to) return false;

        if (!$player->vote_time($to, (int)Kohana::$config->load('balancing.pause.min_interval')))
            $player->log()->add('Deine Sperrzeit ist noch nicht abgelaufen.');
        elseif (count($game->players(true)) == 1 && $game->duration() == 0) {
            $game->recalculate_flow();
            $player->log()->add('Du hast die Spielgeschwindigkeit geändert.');
        } else $player->log()->add('Die Spielgeschwindkeit wird nach Ablauf des aktuellen Ticks angepasst.');

        return $this->japi_data();
    }

    public function japi_favbattle() {
        /**
         * @global $game Model_Game
         * @global $player Model_Player
         * @var $item Model_Items_Abstract_Item
         */
        global $game, $player;

        $video_id = (int)$this->post('v');
        $label = mb_substr($this->post('l'), 0, 127);

        if (!$video_id || !$label || !($chk = Model_Combat_Handler::check_battle($video_id)) || ($game->id() != $chk))
            return $this->render(['success' => 0]);

        Model_Combat_Handler::add_to_gallery($video_id, $player->id(), $label);
        return $this->render(['success' => 1]);
    }

    public function japi_message() {
        /**
         * @global $game Model_Game
         * @global $player Model_Player
         */
        global $game, $player;
        if (!$player->get_status()->alive() || !$game->config('modules.multiplayer'))
            return $this->render(['success' => 0]);

        $action = $this->post('action');

        switch ($action) {
            case 'new':
                $title = $this->post('title');
                $message = $this->post('body');
                $to = (int)$this->post('to');

                if (strlen($title) < 2 || strlen($title) > 64 || strlen($message) < 5 || strlen($message) > 2048)
                    return $this->render(['success' => 0]);

                if ($to == -1) {
                    foreach ($game->players() as $p) if ($p->id() != $player->id()) $p->get_postbox()->add($player->id(), $message, $title);
                } else {
                    if (!($p = $game->get_player($to)) || $p->id() == $player->id())
                        return $this->render(['success' => 0]);
                    $p->get_postbox()->add($player->id(),$message, $title);
                }

                $player->achievements()->achieve(Model_Achievement::MA_LETTERS);
                return $this->render(['success' => 1]);
            case 'delete':
                $player->get_postbox()->remove($this->post('mid'));
                return $this->render(['success' => 1]);
            default: return $this->render(['success' => 0]);
        }
    }

    public function japi_pause() {
        /**
         * @global $game Model_Game
         * @global $player Model_Player
         * @var $item Model_Items_Abstract_Item
         */
        global $game, $player;
        if ($game->timeflow() != 0) return false;

        $set = (int)$this->post('set');

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

        //ToDo: Battle AI settings

        $player->log()->add('Du hast dein Kampfverhalten angepasst.');

        $this->render_notifications();
        return $this->render(['success' => 1]);
    }

    public function japi_mp() {
        /**
         * @global $player Model_Player
         */
        global $player;

        $escort = (int)$this->post('escort');
        $ping = (int)$this->post('ping');

        if ($player->get_postbox()->beacon() && !$ping) $player->get_postbox()->beacon(0);
        elseif (!$player->get_postbox()->beacon() && $ping) $player->get_postbox()->beacon(15);

        $player->companion((bool)$escort);
        return $this->render(['success' => 1]);
    }
}