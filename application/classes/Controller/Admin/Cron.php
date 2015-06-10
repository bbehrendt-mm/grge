<?php

class Controller_Admin_Cron extends Controller {

    protected static $force_ajax = false;
    protected static $force_login = false;

    private function auto_process($any = false, $cleanup = true) {
        $auto_proc = array();
        foreach (DB::select('gameid', 'timestamp')->from('games')->where('timestamp', '<', strtotime($any ? '+1 week' : '-1 week'))->execute()->as_array() as $entry) {
            unset($GLOBALS['game'], $GLOBALS['player']);
            $gameid = $entry['gameid'];
            $timestamp = $entry['timestamp'];

            if (!$any && $timestamp > strtotime('-1 week')) {
                $auto_proc[$gameid] = 'Wird nicht verarbeitet, Alter ist ' . floor((time() - $timestamp)/3600) . ' Stunden.';
                continue;
            }

            $game = new Model_Game();
            try {
                $game->read($gameid);

                if ($game->paused()) {
                    $auto_proc[$gameid] = 'Wird nicht verarbeitet, Spiel ist pausiert';
                    continue;
                }

                if (!$cleanup) {
                    $auto_proc[$gameid] = 'Spieldaten aktualisiert';
                    $game->check_players();
                    $game->write();
                    continue;
                }

                if (!count($game->players(false))) {
                    $auto_proc[$gameid] = 'Spiel entfernt: Keine Spieler!';
                    $game->check_players();
                    continue;
                }

                $dropped = 0;
                foreach ($game->players(false) as $p)
                    if (!$p->alive())
                        if ($game->retire($p->user_id(), true)) {
                            $dropped++;
                            DB::update('users')->set(array('session' => '#'))->where('uid', '=', $p->user_id())->execute();
                        }

                $game->check_players();
                $game->write();

                $auto_proc[$gameid] = "Automatischer Dropout: $dropped Spieler";
            } catch (Exception $e) {
                $auto_proc[$gameid] = 'EXCEPTION: ' . $e->getFile() . '[' . $e->getLine() . ']' . ' - ' . $e->getMessage();
            }
        }

        return $auto_proc;
    }

    private function cleanup() {
        $junk = DB::select(DB::expr('DISTINCT(`gameid`)'))->from('games_cloud')->where('gameid', 'NOT IN', DB::select('gameid')->from('games'))->execute()->as_array();

        $garbage = array();
        foreach ($junk as $entry) {
            $garbage[] = $entry['gameid'];
            DB::delete('games_cloud')->where('gameid', '=', $entry['gameid'])->execute();
        }

        DB::update('users')->set(array('session' => null))->execute();

        return $garbage;
    }

    private function read_error_log($time) {
        $file = APPPATH . "logs/" . date('Y', $time) . "/" . date('m', $time) . "/" . date('d', $time) . EXT;
        if (!file_exists($file)) return array();

        $lines = file($file);

        $ret = array();
        $c1 = $c2 = null;
        foreach ($lines as $line) {
            if (!preg_match('/(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}) -{3} (\w*): (.*)/', $line, $matches)) {
                if (!$c1 || !$c2) continue;
                if (!isset($ret[$c1]) || !isset($ret[$c1][$c2])) continue;
            } else {
                list(,$c1, $c2, $line) = $matches;
                if (!isset($ret[$c1])) $ret[$c1] = array($c2 => array());
                if (!isset($ret[$c1][$c2])) $ret[$c1][$c2] = array();
            }
            $ret[$c1][$c2][] = $line;
        }
        return $ret;
    }

    public function action_main() {
        if (Kohana::$config->load('server.externals.cronjob.token') != $this->request->param('key')) die('invalid key');
        $out = isset($_GET['out']) ? explode(',',$_GET['out']) : array('mail,screen,file');
        $mode = isset($_GET['mode']) ? $_GET['mode'] : 'logs';
        $day = isset($_GET['day']) ? $_GET['day'] : 't';

        $t = strtotime(($day == 'y') ? '-1 day' : 'today');
        $auto_proc = $garbage = null;
        switch ($mode) {
            case 'logs':
                $auto_proc = null;
                $garbage = null;
                break;
            case 'proc':
                $auto_proc = $this->auto_process(true, false);
                $garbage = null;
                break;
            case 'daily':
                $auto_proc = $this->auto_process();
                $garbage = $this->cleanup();
                break;
        }

        // Get version data, append profiling information when this is not a stable version
        $version_data = Kohana::$config->load('build.version');
        $content = View::factory('cron')
            ->set('baseurl', URL::base('http'))
            ->set('version', "GRGE {$version_data['major']}.{$version_data['minor']}.{$version_data['service']}-{$version_data['maintenance']}-{$version_data['stage']}-{$version_data['build']} ({$version_data['date']})")
            ->set('autoprc', $auto_proc)
            ->set('garbage', $garbage)
            ->set('errors', $this->read_error_log($t))->render();

        foreach ($out as $target)
            switch ($target) {
                case 'screen':
                    echo $content;
                    break;
                case 'file':
                    file_put_contents('application/reports/' . date('Y-m-d', $t) . '-' . $mode . '.php', "<?php defined('SYSPATH') OR die('No direct script access.'); ?>\n" . $content);
                    break;
                case 'mail': default:
                $mail_to = implode(',',(array)Kohana::$config->load('server.externals.cronjob.to'));
                $mail_title = 'ZombVival System Report ' . date('d.m.Y', strtotime('-1 day'));
                $header = "MIME-Version: 1.0\r\nContent-type: text/html; charset=iso-8859-1\r\nFrom: ZombVival Report System <" . Kohana::$config->load('server.externals.cronjob.from') . '@' . $_SERVER["SERVER_NAME"] . ">";
                $ret = mail($mail_to, $mail_title, $content, $header);
                if (!in_array('screen',$out))
                    echo $ret ? "Mail delivery OK\n" : 'Mail delivery FAILED\n';
            }
    }

}