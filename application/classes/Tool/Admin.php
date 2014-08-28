<?php defined('SYSPATH') OR die('No direct access allowed.');

class Tool_Admin {

    private static $admin_checked = false;

    public static function check_privileges() {
        return Session::instance()->get('admin',NULL) >= (time() - 600);
    }

    private static function head($action) {
        Syslogd::sprintln($action);
        Syslogd::in();
        if (!static::$admin_checked) {
            Syslogd::sprint('Prüfe Berechtigungen... ');
            if (static::check_privileges()) {
                Syslogd::sprintln('OK');
                static::$admin_checked = true;
                return true;
            } else {
                Syslogd::sprintln('FEHLGESCHLAGEN');
                Syslogd::out();
                return false;
            }
        } else return true;
    }

    private static function tail($success) {
        Syslogd::out();
        return $success;
    }

    public static function maintenance_activate($message) {
        if (!static::head('Wartungsmodus aktivieren'))
            return false;

        Syslogd::sprint("Schreibe syslock.f ... ");
        $f = fopen('syslock.f', 'w');
        $r = fwrite($f, $message);
        fclose($f);

        if ($f !== false) Syslogd::sprintln("OK, {$r} Bytes geschrieben");
        else Syslogd::sprintln("FEHLGESCHLAGEN");

        return static::tail(!($f === false));
    }

    public static function maintenance_deactivate() {
        if (!static::head('Wartungsmodus deaktivieren'))
            return false;

        Syslogd::sprint("Entferne syslock.f ... ");
        if (!file_exists('syslock.f')) Syslogd::sprintln("DATEI NICHT GEFUNDEN");
        else {
            unlink('syslock.f');
            Syslogd::sprintln("OK");
        }

        return static::tail(true);
    }

    public static function secure_integrity() {
        if (!static::head('Integritätssicherung'))
            return false;

        Syslogd::sprintln("Inventur durchführen");
        Syslogd::in();

        Syslogd::sprint("Hauptindexspeicher... ");
        $main = array();
        $ret = DB::select('gameid')->from('games')->execute()->as_array();
        foreach ($ret as $line)
            $main[] = $line['gameid'];
        Syslogd::sprintln(count($main) . " Einträge");

        Syslogd::sprint("Userreferenzspeicher... ");
        $references = array();
        $ret = DB::select('gameid')->from('xref_game_player')->execute()->as_array();
        foreach ($ret as $line)
            $references[] = $line['gameid'];
        Syslogd::sprintln(count($references) . " Einträge");

        Syslogd::sprint("Lobbyreferenzspeicher... ");
        $references = array();
        $ret = DB::select('gameid')->from('multiplayer_lobby')->execute()->as_array();
        foreach ($ret as $line)
            if (!in_array($line['gameid'], $references))
                $references[] = $line['gameid'];
        Syslogd::sprintln(count($ret) . " Einträge");

        Syslogd::out();
        Syslogd::sprint("Prüfe vollständigen Abschluss aller Objekte... ");
        $deathlist = array_merge(array_diff($main, $references), array_diff($references, $main));
        Syslogd::sprintln(count($deathlist) . " nicht auflösbare Referenzen");


        Syslogd::sprintln("Cloudshard-Integrität prüfen");
        Syslogd::in();
        $cloudref = array();
        Syslogd::sprint("Suche Cloudshard-Referenzen... ");
        DB::select('gameid')->from('games_cloud')->group_by('gameid')->execute()->as_array();
        foreach ($ret as $line)
            $cloudref[] = $line['gameid'];
        Syslogd::sprintln(count($cloudref) . " Referenzgruppen");

        Syslogd::sprint("Prüfe Gültigkeit... ");
        $i = 0;
        foreach ($cloudref as $id)
            if (!in_array($id, $main)) {
                $i++;
                if (!in_array($id, $deathlist))
                    $deathlist[] = $id;
            }
        Syslogd::sprintln($i . " ungültige Referenzen");
        Syslogd::out();

        if (count($deathlist) > 0) {
            Syslogd::sprintln("Integritätsprobleme: " . count($deathlist));
            Syslogd::in();
            foreach ($deathlist as $gameid)
                static::purge_game($gameid);
            Syslogd::out();
        } else Syslogd::sprintln("Keine Integritätsprobleme gefunden");

        return static::tail(true);
    }

    public static function purge_xref($gameid, $pid) {
        if (!static::head('Entferne Referenz: P' . $pid . ' zu {#' . $gameid . '}'))
            return false;

        Syslogd::sprint("Userreferenz... ");
        DB::delete('xref_game_player')->where('gameid', '=', $gameid)->where('uid', '=', $pid)->execute();
        Syslogd::sprintln("OK");

        return static::tail(true);
    }

    public static function purge_game($gameid) {
        if ($gameid == -1) {
            if (!static::head('Vollständige Löschung: Alle Spiele'))
                return false;

            foreach (DB::select('gameid')->from('games')->execute()->as_array() as $line)
               static::purge_game($line['gameid']);

            return static::tail(true);
        } else {
            if (!static::head('Vollständige Löschung: {#' . $gameid . '}'))
                return false;

            Syslogd::sprint("Hauptindex... ");
            DB::delete('games')->where('gameid', '=', $gameid)->execute();
            Syslogd::sprintln("OK");

            Syslogd::sprint("Userreferenz... ");
            $users = DB::select('uid')->from('xref_game_player')->where('gameid', '=', $gameid)->execute()->as_array();
            DB::delete('xref_game_player')->where('gameid', '=', $gameid)->execute();
            Syslogd::sprintln("OK");

            Syslogd::sprint("Lobbyreferenz... ");
            DB::delete('multiplayer_lobby')->where('gameid', '=', $gameid)->execute();
            Syslogd::sprintln("OK");

            Syslogd::sprint("Cloudshards... ");
            DB::delete('games_cloud')->where('gameid', '=', $gameid)->execute();
            Syslogd::sprintln("OK");

            if (count($users) == 0)
                Syslogd::sprintln("Keine verknüpften Benutzersessions gefunden");
            else {
                Syslogd::sprintln(count($users) . " verknüpfte Sessions zurücksetzen");
                Syslogd::in();
                foreach ($users as $user)
                    static::reset_user($user['uid']);
                Syslogd::out();
            }

            return static::tail(true);
        }
    }

    public static function reset_user($uid) {
        if (!static::head('Benutzersitzung zurücksetzen: ' . ($uid != -1 ? ('{#' . $uid . '}') : 'Alle')))
            return false;

        Syslogd::sprint("Datenbank aktualisieren... ");
        $tmp = DB::update('users')->set(array('session' => '#'));
        if ($uid != -1) $tmp->where('uid', '=', $uid);
        $tmp->execute();
        Syslogd::sprintln("OK");

        return static::tail(true);
    }

    public static function delete_user($uid) {
        if (!static::head('Benutzer entfernen: {#' . $uid . '}'))
            return false;

        Syslogd::sprint("Benutzereintrag entfernen... ");
        DB::delete('users')->where('uid', '=', $uid)->execute();
        Syslogd::sprintln("OK");

        Syslogd::sprint("Auszeichnungen entfernen... ");
        DB::delete('achievements')->where('uid', '=', $uid)->execute();
        Syslogd::sprintln("OK");

        Syslogd::sprint("Ranking-Einträge entfernen... ");
        DB::delete('ranking')->where('uid', '=', $uid)->execute();
        Syslogd::sprintln("OK");

        return static::tail(true);
    }

    public static function ban($uid, $ban) {
        if (!static::head('Bannstatus setzen: {#' . $uid . '} auf ' . $ban))
            return false;

        Syslogd::sprint("Datenbank aktualisieren... ");
        DB::update('users')->set(array('ban' => $ban))->where('uid', '=', $uid)->execute();
        Syslogd::sprintln("OK");

        static::reset_user($uid);

        return static::tail(true);
    }

    public static function achieve($uid, $aid, $count, $game) {
        if (!static::head('Auszeichnung verleihen: {#' . $uid . '}, ' . Model_Achievement::decode_aid($aid) . ' x ' . $count . ', gameid ist ' . $game))
            return false;

        Syslogd::sprint("Suche alte Auszeichnungseinträge... ");
        $res = DB::select('value')->from('achievements')->where('gameid', '=', $game)->and_where('uid', '=', $uid)->and_where('aid', '=', $aid)->execute()->as_array();
        if (count($res) > 0) {
            Syslogd::sprintln($res[0]['value'] . " Einträge gefunden!");
            $count += $res[0]['value'];

            Syslogd::sprint("Lösche alte Auszeichnungseinträge... ");
            DB::delete('achievements')->where('gameid', '=', $game)->and_where('uid', '=', $uid)->and_where('aid', '=', $aid)->execute();
            Syslogd::sprintln("OK");
        } else Syslogd::sprintln("Keine gefunden");

        Syslogd::sprint("Erzeuge neuen Eintrag... ");
        DB::insert('achievements', Array('uid', 'gameid', 'aid', 'value'))->values(Array($uid, $game, $aid, $count))->execute();
        Syslogd::sprintln("OK");

        return static::tail(true);
    }

    public static function create_game($name, $lang, $slots) {
        if (!static::head('Erzeuge MP-Partie: ' . $lang . '/' . $name . ', ' . $slots . ' Slots'))
            return false;

        global $game;
        $tmp = $game;

        Syslogd::sprint("Erzeuge Index...");
        $game = new Model_Game(false);
        $id = $game->start(10000, 1, 300, null, $name);
        Syslogd::sprintln("OK; ID: " . $id);

        $game = $tmp;

        Syslogd::sprint("Erzeuge Lobbyeintrag...");
        DB::insert('multiplayer_lobby', array('gameid', 'lang', 'slots', 'name', 'timestamp'))->values(array($id, $lang, $slots, $name, time()))->execute() ;
        Syslogd::sprintln("OK");

        return static::tail(true);
    }

    public static function void_ranking($season) {
        if (!static::head('Ranking für Season ' . $season . ' zurücknehmen'))
            return false;

        DB::delete('achievements')->where('gameid', '=', -100-$season)->execute();

        return static::tail(true);
    }

    public static function finalize_ranking($season) {
        if (!static::head('Ranking für Season ' . $season . ' abschließen'))
            return false;

        static::void_ranking($season);

        $modes = Array(1000,1100,2000,3000,4000);
        $modes_mp = Array(10000);
        $flows = Array(0,1);

        foreach ($modes as $mode) {
            Syslogd::sprintln("Verarbeite Ranking #{$mode}");
            Syslogd::in();
            $points = Array();
            foreach ($flows as $flow) {
                Syslogd::sprintln("Verarbeite Ranking #{$mode}/{$flow}");
                Syslogd::in();

                Syslogd::sprint("Lade Ranking... ");
                $ranks = DB::select('uid')->from('ranking')->where('season', '=', $season)->and_where('board', '=', $mode)->and_where('flow', '=', $flow)->and_where('start', '>=', 0)->order_by('points', 'DESC')->order_by('ticks', 'DESC')->limit(10)->execute()->as_array();
                Syslogd::sprintln("OK");

                Syslogd::sprintln("Berechne Auszeichnungen... ");
                Syslogd::in();

                foreach ($ranks as $place => $rank) {

                    if		($place == 0) $point = 10;
                    elseif	($place == 1) $point = 5;
                    elseif	($place == 2) $point = 3;
                    else				  $point = 1;

                    Syslogd::sprintln("Platz {$place}: Spieler #{$rank['uid']}, {$point} Punkte");
                    if (isset($points[$rank['uid']])) $points[$rank['uid']] += $point;
                    else $points[$rank['uid']] = $point;
                }
                Syslogd::out();
                Syslogd::out();
            }

            Syslogd::sprintln("Vergebe Auszeichnungen");
            Syslogd::in();
            foreach ($points as $uid => $value)
                static::achieve($uid, $mode, $value, -100-$season);
            Syslogd::out();
            Syslogd::out();
        }

        foreach ($modes_mp as $mode) {
            Syslogd::sprintln("Verarbeite MP-Ranking #{$mode}");
            Syslogd::in();
            $points = Array();

            Syslogd::sprint("Lade Ranking... ");
            $ranks = DB::select(array(DB::expr('GROUP_CONCAT(`uid` SEPARATOR \';\')'), 'uids'))->from('ranking_mp')->join('ranking', 'LEFT')->on('ranking_mp.gameid', '=', 'ranking.gameid')->on('ranking_mp.season', '=', 'ranking.season')->where('ranking_mp.season', '=', $season)->and_where('ranking_mp.board', '=', $mode)->group_by('ranking_mp.gameid')->order_by('ranking_mp.points', 'DESC')->limit(10)->execute()->as_array();
            Syslogd::sprintln("OK");

            Syslogd::sprintln("Berechne Auszeichnungen... ");
            Syslogd::in();

            foreach ($ranks as $place => $rank) {

                if		($place == 0) $point = 10;
                elseif	($place == 1) $point = 5;
                elseif	($place == 2) $point = 3;
                else				  $point = 1;

                if (!$rank['uids'])
                    Syslogd::sprintln("Platz {$place}: Keine Spieler!");
                else foreach (explode(';', $rank['uids']) as $uid) {
                    Syslogd::sprintln("Platz {$place}: Spieler #{$uid}, {$point} Punkte");
                    if (isset($points[$uid])) $points[$uid] += $point;
                    else $points[$uid] = $point;
                }

            }
            Syslogd::out();

            Syslogd::sprintln("Vergebe Auszeichnungen");
            Syslogd::in();
            foreach ($points as $uid => $value)
                static::achieve($uid, $mode, $value, -100-$season);
            Syslogd::out();
            Syslogd::out();
        }

        Syslogd::sprintln("Verarbeite Mentorenranking");
        Syslogd::in();

        Syslogd::sprint("Lade Ranking... ");
        $ranks = DB::select(Array(DB::expr('SUM(`points`)'), 'points'), Array('mentor.mentor', 'uid'))->from('ranking')->join('mentor')->on('mentor.uid', '=', 'ranking.uid')->group_by('mentor.mentor')->where('season', '=', $season)->and_where('mentor.mentor', '>', 0)->limit(10)->order_by('points', 'DESC')->execute()->as_array();
        Syslogd::sprintln("OK");

        Syslogd::sprintln("Berechne und vergebe Auszeichnungen... ");
        Syslogd::in();

        foreach ($ranks as $place => $rank) {

            if		($place == 0) $point = 10;
            elseif	($place == 1) $point = 5;
            elseif	($place == 2) $point = 3;
            else				  $point = 1;

            Syslogd::sprintln("Platz {$place}: Spieler #{$rank['uid']}, {$point} Punkte");
            if (isset($points[$rank['uid']])) $points[$rank['uid']] += $point;
            else $points[$rank['uid']] = $point;

            static::achieve($rank['uid'], 41, $point, -100-$season);
        }

        Syslogd::out();
        Syslogd::out();

        return static::tail(true);
    }

}