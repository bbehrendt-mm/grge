<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Admin_Ranking extends Controller_Admin_Admin {

    protected static $auto_require = ['RANKING'];

    private function get_ranking_data($use_season = null) {
        $banned_users = array_map(function($a) {return $a['user'];},DB::select('user')->from('user_flags')->where('relation','=','DENY')->where('data','=','WHITELIST')->execute()->as_array());

        if ($use_season == null) {
            $use_season = [];
            for ($s = 0; $s <= (int)Kohana::$config->load('server.season'); $s++)
                $use_season[] = $s;
        } elseif (!is_array($use_season)) $use_season = [$use_season];

        $data = [];
        $user_name_cache = [];
        foreach ($use_season as $s) {
            $data[$s] = [];
            foreach (Tool_Gamemodes::get_singleplayer_modes() as $mode) {
                $data[$s][$mode] = [];
                for ($flow = 0; $flow < 2; $flow++) {
                    $result = DB::select('uid','gameid')->from('ranking')->where('uid', 'NOT IN', $banned_users)->where('season', '=', $s)->and_where('board', '=', $mode)->and_where('flow', '=', $flow)->and_where('start', '>=', 0)->order_by('points', 'DESC')->order_by('ticks', 'DESC')->limit(10)->execute()->as_array();
                    if (!$result) continue;
                    $data[$s][$mode][$flow] = $result;
                    foreach ($data[$s][$mode][$flow] as $id => $rank) {
                        $uid = (int)$rank['uid'];
                        if (!isset($user_name_cache[$uid])) $user_name_cache[$uid] = Model_User::name_by_id($uid);
                        $data[$s][$mode][$flow][$id]['uid'] = [$uid => $user_name_cache[$uid]];
                    }

                }
            }

            foreach (Tool_Gamemodes::get_multiplayer_modes() as $mode) {
                $data[$s][$mode] = [];
                $flow = 1;
                $result = DB::select(array(DB::expr('GROUP_CONCAT(`uid` SEPARATOR \';\')'), 'uid'), 'ranking_mp.gameid')->from('ranking_mp')->join('ranking', 'LEFT')->on('ranking_mp.gameid', '=', 'ranking.gameid')->on('ranking_mp.season', '=', 'ranking.season')->where('ranking_mp.season', '=', $s)->and_where('ranking_mp.board', '=', $mode)->group_by('ranking_mp.gameid')->order_by('ranking_mp.points', 'DESC')->limit(10)->execute()->as_array();
                if (!$result) continue;
                $data[$s][$mode][$flow] = $result;
                foreach ($data[$s][$mode][$flow] as $id => $rank) {
                    $c = [];
                    foreach (explode(';', $rank['uid']) as $uid) {
                        if (!($uid = (int)$uid)) continue;
                        if (!isset($user_name_cache[$uid])) $user_name_cache[$uid] = Model_User::name_by_id($uid);
                        $c[$uid] = $user_name_cache[$uid];
                    }
                    $data[$s][$mode][$flow][$id]['uid'] = $c;
                }
            }
        }

        return $data;
    }

    private function extract_achievement_data($ranks) {
        $current_season = (int)Kohana::$config->load('server.season');

        $results = [];
        foreach ($ranks as $season => $data_s) {
            $results[$season] = [];
            foreach ($data_s as $mode => $data_m) {
                $results[$season][$mode] = [];
                foreach ($data_m as $data_f) {
                    foreach ($data_f as $p => $rank) {
                        if		($p == 0) $points = 10;
                        elseif	($p == 1) $points = 5;
                        elseif	($p == 2) $points = 3;
                        else			  $points = 1;

                        foreach ($rank['uid'] as $uid => $name) {
                            if (!isset($results[$season][$mode][$uid]))
                                $results[$season][$mode][$uid] = ['name' => $name, 'points' => 0, 'ok' => false, 'achievements' => (int)DB::select('value')->from('achievements')->where('aid','=',$mode)->where('season','=',-1)->where('gameid','=',-100-$season)->where('uid','=',$uid)->execute()->get('value')];
                            $results[$season][$mode][$uid]['points'] += $points;
                        }
                    }

                    foreach ($results[$season][$mode] as $uid => $data) {
                        $t = $results[$season][$mode][$uid];
                        $results[$season][$mode][$uid]['ok'] = ($t['achievements'] == 0 && $season == $current_season) || ($t['achievements'] == $t['points']);
                    }
                }
            }
        }

        return $results;
    }

    private function error_summary($achievements) {
        $results = [];
        foreach ($achievements as $season => $data_s) {
            $results[$season] = 0;
            foreach ($data_s as $mode => $data_m)
                foreach ($data_m as $uid => $data)
                    $results[$season] += $data['ok'] ? 0 : 1;
        }
        return $results;
    }

    public function japi_fix() {
        $season = (int)$this->request->current()->post('season');

        if ($season < 0 || $season >= (int)Kohana::$config->load('server.season'))
            return $this->render(['success' => 0]);

        $data = $this->get_ranking_data($season);
        $achievements = $this->extract_achievement_data($data);
        $errors = $this->error_summary($achievements);

        if (!$errors[$season]) return $this->render(['success' => 0]);

        DB::delete('achievements')->where('gameid','=',-100-$season)->where('season','=','-1')->execute();

        foreach ($achievements as $season => $data_s)
            foreach ($data_s as $mode => $data_m)
                foreach ($data_m as $uid => $data)
                    DB::insert('achievements', ['uid', 'gameid', 'aid', 'value','season'])->values([$uid, -100-$season, $mode, $data['points'], -1])->execute();

        return $this->render(['success' => 1]);
    }

    public function action_main() {
        $season = (int)Kohana::$config->load('server.season');
        $modes_tmp = array_merge(Tool_Gamemodes::get_singleplayer_modes(), Tool_Gamemodes::get_multiplayer_modes());
        $modes = [];
        foreach ($modes_tmp as $mode)
            $modes[$mode] = Tool_Gamemodes::get_board_by_id($mode);

        $data = $this->get_ranking_data();
        $achievements = $this->extract_achievement_data($data);

        $this->add_widget(View::factory('admin/ranking')
            ->set('season', $season)
            ->set('modes', $modes)
            ->set('ranks', $data)
            ->set('achievements', $achievements)
            ->set('errors', $this->error_summary($achievements))
            ->render());

        $this->render();
    }
}