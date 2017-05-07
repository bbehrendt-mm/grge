<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Player extends Controller_Game {

    protected static $death_allowed_actions = ['favbattle'];

    public static function battle_ai_ammo_types() {
        return ['Model_Items_Battery','Model_Items_Ammo','Model_Items_Splinter','Model_Items_Bolts'];
    }

    public function japi_reflux() {
        if (Globals::CurrentGame()->timeflow() != 1) return false;

        $to = (int)$this->post('set');
        if ($to < 0 || $to > 6 || Globals::PrimaryPlayer()->vote_time() == $to) return false;

        if (!Globals::PrimaryPlayer()->vote_time($to, (int)Kohana::$config->load('balancing.pause.min_interval')))
            Globals::PrimaryPlayer()->log()->add('Deine Sperrzeit ist noch nicht abgelaufen.');
        elseif (count(Globals::CurrentGame()->players(true)) == 1 && Globals::CurrentGame()->duration() == 0) {
            Globals::CurrentGame()->recalculate_flow();
            Globals::PrimaryPlayer()->log()->add('Du hast die Spielgeschwindigkeit geändert.');
        } else Globals::PrimaryPlayer()->log()->add('Die Spielgeschwindkeit wird nach Ablauf des aktuellen Ticks angepasst.');

        return $this->japi_data();
    }

    public function japi_favbattle() {
        $video_id = (int)$this->post('v');
        $label = mb_substr($this->post('l'), 0, 127);

        if (!$video_id || !$label || !($chk = Model_Combat_Handler::check_battle($video_id)) || (Globals::CurrentGame()->id() != $chk))
            return $this->render(['success' => 0]);

        Model_Combat_Handler::add_to_gallery($video_id, Globals::PrimaryPlayer()->id(), $label);
        return $this->render(['success' => 1]);
    }

    public function japi_message() {
        if (!Globals::PrimaryPlayer()->get_status()->alive() || !Globals::CurrentGame()->config('modules.multiplayer'))
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
                    foreach (Globals::CurrentGame()->players() as $p) if ($p->id() != Globals::PrimaryPlayer()->id()) $p->get_postbox()->add(Globals::PrimaryPlayer()->id(), $message, $title);
                } else {
                    if (!($p = Globals::CurrentGame()->get_player($to)) || $p->id() == Globals::PrimaryPlayer()->id())
                        return $this->render(['success' => 0]);
                    $p->get_postbox()->add(Globals::PrimaryPlayer()->id(),$message, $title);
                }

                Globals::PrimaryPlayer()->achievements()->achieve(Model_Achievement::MA_LETTERS);
                return $this->render(['success' => 1]);
            case 'delete':
                Globals::PrimaryPlayer()->get_postbox()->remove($this->post('mid'));
                return $this->render(['success' => 1]);
            default: return $this->render(['success' => 0]);
        }
    }

    public function japi_pause() {
        if (Globals::CurrentGame()->timeflow() != 0) return false;

        $set = (int)$this->post('set');

        if ($set) {
            if (Globals::CurrentGame()->paused()) return false;
            if (Globals::CurrentGame()->pauselock() > (time() - Kohana::$config->load('balancing.pause.min_interval'))) {
                Globals::PrimaryPlayer()->log()->add('Deine Sperrzeit ist noch nicht abgelaufen.');
                return $this->japi_data();
            } else {
                Globals::CurrentGame()->pause();
                return $this->render(['redirect' => 'game/redirect']);
            }
        } else {
            if (!Globals::CurrentGame()->paused()) return false;
            if (Globals::CurrentGame()->pauselock() > (time() - Kohana::$config->load('balancing.pause.min_duration'))) {
                $this->add_note('error', __('Deine Sperrzeit ist noch nicht abgelaufen.'));
                return $this->render();
            } else {
                Globals::CurrentGame()->unpause();
                return $this->render(['redirect' => 'game/redirect']);
            }
        }
    }

    public function japi_ai() {
        $s = $this->post('ai');
        if (!$s || !Globals::PrimaryPlayer()->ai($s))
            return $this->render(['success' => 0]);

        Globals::PrimaryPlayer()->log()->add('Du hast dein Kampfverhalten angepasst.');

        $this->render_notifications();
        return $this->render(['success' => 1]);
    }

    public function japi_mp() {
        $escort = (int)$this->post('escort');
        $ping = (int)$this->post('ping');

        if (Globals::PrimaryPlayer()->get_postbox()->beacon() && !$ping) Globals::PrimaryPlayer()->get_postbox()->beacon(0);
        elseif (!Globals::PrimaryPlayer()->get_postbox()->beacon() && $ping) Globals::PrimaryPlayer()->get_postbox()->beacon(15);

        Globals::PrimaryPlayer()->companion((bool)$escort);
        return $this->render(['success' => 1]);
    }
}