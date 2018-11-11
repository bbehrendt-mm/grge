<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Player extends Controller_Game {

    protected static $death_allowed_actions = ['favbattle'];

    public static function battle_ai_ammo_types() {
        return [Model_Items_Battery::cls(),Model_Items_Ammo::cls(),Model_Items_Splinter::cls(),Model_Items_Bolts::cls()];
    }

    public function japi_reflux() {
        if (Globals::CurrentGameF()->timeflow() != 1) return false;

        $to = (int)self::post('set');
        if ($to < 0 || $to > 6 || Globals::PrimaryPlayerF()->vote_time() == $to) return false;

        if (!Globals::PrimaryPlayerF()->vote_time($to, (int)Kohana::$config->load('balancing.pause.min_interval')))
            Globals::PrimaryPlayerF()->log()->add('Deine Sperrzeit ist noch nicht abgelaufen.');
        elseif (count(Globals::CurrentGameF()->players(true)) == 1 && Globals::CurrentGameF()->duration() == 0) {
            Globals::CurrentGameF()->recalculate_flow();
            Globals::PrimaryPlayerF()->log()->add('Du hast die Spielgeschwindigkeit geändert.');
        } else Globals::PrimaryPlayerF()->log()->add('Die Spielgeschwindkeit wird nach Ablauf des aktuellen Ticks angepasst.');

        return $this->japi_data();
    }

    public function japi_favbattle() {
        $video_id = (int)self::post('v');
        $label = mb_substr(self::post('l'), 0, 127);

        if (!$video_id || !$label || !($chk = Model_Combat_Handler::check_battle($video_id)) || (Globals::CurrentGameF()->id() != $chk))
            return $this->render(['success' => 0]);

        Model_Combat_Handler::add_to_gallery($video_id, Globals::PrimaryPlayerF()->id(), $label);
        return $this->render(['success' => 1]);
    }

    public function japi_message() {
        if (!Globals::PrimaryPlayerF()->get_status()->alive() || !Globals::CurrentGameF()->config('modules.multiplayer'))
            return $this->render(['success' => 0]);

        $action = self::post('action');

        switch ($action) {
            case 'new':
                $title = self::post('title');
                $message = self::post('body');
                $to = (int)self::post('to');

                if (strlen($title) < 2 || strlen($title) > 64 || strlen($message) < 5 || strlen($message) > 2048)
                    return $this->render(['success' => 0]);

                if ($to == -1) {
                    foreach (Globals::CurrentGameF()->players() as $p) if ($p->id() != Globals::PrimaryPlayerF()->id()) $p->get_postbox()->add(Globals::PrimaryPlayerF()->id(), $message, $title);
                } else {
                    if (!($p = Globals::CurrentGameF()->get_player($to)) || $p->id() == Globals::PrimaryPlayerF()->id())
                        return $this->render(['success' => 0]);
                    $p->get_postbox()->add(Globals::PrimaryPlayerF()->id(),$message, $title);
                }

                Globals::PrimaryPlayerF()->achievements()->achieve(Model_Achievement::MA_LETTERS);
                return $this->render(['success' => 1]);
            case 'delete':
                Globals::PrimaryPlayerF()->get_postbox()->remove(
                    self::post('mid'));
                return $this->render(['success' => 1]);
            default: return $this->render(['success' => 0]);
        }
    }

    public function japi_pause() {
        if (Globals::CurrentGameF()->timeflow() != 0) return false;

        $set = (int)self::post('set');

        if ($set) {
            if (Globals::CurrentGameF()->paused()) return false;
            if (Globals::CurrentGameF()->pauselock() > (time() - Kohana::$config->load('balancing.pause.min_interval'))) {
                Globals::PrimaryPlayerF()->log()->add('Deine Sperrzeit ist noch nicht abgelaufen.');
                return $this->japi_data();
            } else {
                Globals::CurrentGameF()->pause();
                return $this->render(['redirect' => 'game/redirect']);
            }
        } else {
            if (!Globals::CurrentGameF()->paused()) return false;
            if (Globals::CurrentGameF()->pauselock() > (time() - Kohana::$config->load('balancing.pause.min_duration'))) {
                $this->add_note('error', __('Deine Sperrzeit ist noch nicht abgelaufen.'));
                return $this->render();
            } else {
                Globals::CurrentGameF()->unpause();
                return $this->render(['redirect' => 'game/redirect']);
            }
        }
    }

    public function japi_ai() {
        $s = self::post('ai');
        if (!$s || !Globals::PrimaryPlayerF()->ai($s))
            return $this->render(['success' => 0]);

        Globals::PrimaryPlayerF()->log()->add('Du hast dein Kampfverhalten angepasst.');

        $this->render_notifications();
        return $this->render(['success' => 1]);
    }

    public function japi_mp() {
        $escort = (int)self::post('escort');
        $ping = (int)self::post('ping');

        if (Globals::PrimaryPlayerF()->get_postbox()->beacon() && !$ping) Globals::PrimaryPlayerF()->get_postbox()->beacon(0);
        elseif (!Globals::PrimaryPlayerF()->get_postbox()->beacon() && $ping) Globals::PrimaryPlayerF()->get_postbox()->beacon(15);

        Globals::PrimaryPlayerF()->companion((bool)$escort);
        return $this->render(['success' => 1]);
    }
}