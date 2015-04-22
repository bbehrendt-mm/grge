<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Ranking extends Controller {

    protected static $force_login = true;
    protected static $menu = 'logout';

    private function get_ranking_data_sp(&$raw_count, $season = null, $mode = null, $job = null, $flow = null, $player = null, $offset = null, $count = null) {
        $base_query = DB::select('season', 'gameid', 'points', 'ticks', 'job', 'board', 'start', 'end', 'name', 'ranking.uid')->from('ranking')->join('users')->on('ranking.uid', '=', 'users.uid')->where('users.uid', 'NOT IN', DB::select('user')->from('user_flags')->where('relation','=','DENY')->where('data','=','WHITELIST'));

        if ($season !== null) $base_query->where('season', '=', $season);
        if ($mode !== null) $base_query->where('board', '=', $mode);
        if ($job !== null) $base_query->where('job', '=', $mode);
        if ($flow !== null) $base_query->where('flow', '=', $flow);
        if ($player !== null) $base_query->where('users.uid', '=', $player);

        $data = $base_query->order_by('points', 'DESC')->order_by('ticks', 'DESC')->execute()->as_array();
        $raw_count = count($data);
        array_unshift($data, true);
        return array_slice($data, $offset === null ? 1 : (1 + $offset), $count, true);
    }

    private function get_ranking_data_mp(&$raw_count, $season = null, $mode = null, $offset = null, $count = null) {
        $base_query = DB::select('ranking_mp.season','ranking_mp.gameid', 'ranking_mp.board', 'ranking_mp.name', 'ranking_mp.points', 'users.uid', array('ranking.ticks', 'pticks'),array('ranking.points', 'ppoints'), array('ranking.job', 'job'), array('users.name', 'player'))->from('ranking_mp')->join('ranking', 'LEFT')->on('ranking_mp.gameid', '=', 'ranking.gameid')->on('ranking_mp.season', '=', 'ranking.season')->join('users', 'LEFT')->on('ranking.uid', '=', 'users.uid');

        if ($season !== null) $base_query->where('ranking_mp.season', '=', $season);
        if ($mode !== null) $base_query->where('ranking_mp.board', '=', $mode);

        $data = $base_query->order_by('ranking_mp.points', 'DESC')->order_by('ranking_mp.points', 'DESC')->execute()->as_array();

        $ranks = Array();
        foreach ($data as $line) {
            $adr = "{$line['season']}-{$line['gameid']}";
            if (!isset($ranks[$adr]))
                $ranks[$adr] = array();

            $ranks[$adr][] = $line;
        }
        $ranks = array_values($ranks);

        $raw_count = count($ranks);
        array_unshift($ranks, true);
        return array_slice($ranks, $offset === null ? 1 : (1 + $offset), $count, true);
    }

    private function convert_data($lists, $is_mp = false) {
        return $is_mp ? array_map(function($element) {
            $tmp = [
                'season'    => $element[0]['season'],
                'id'        => $element[0]['gameid'],
                'score'     => $element[0]['points'],
                'name'      => $element[0]['name'],
                'duration'  => false,
                'mode'      => __(Tool_Modes::get_mode_by_id($element[0]['board'])),
                'players'   => []
            ];

            if ($element[0]['uid'])
                foreach ($element as $sub)
                    $tmp['players'][] = [
                        'name'  => $sub['player'],
                        'id'    => $sub['uid'],
                        'job'   => __(Tool_Modes::get_job_by_id($sub['job'])),
                        'life'  => Tool_Numerics::duration_to_string($sub['pticks']),
                        'score' => $sub['ppoints']
                    ];

            return $tmp;
        }, $lists) : array_map(function($element) {
            return [
                'season'    => $element['season'],
                'id'        => $element['gameid'],
                'score'     => $element['points'],
                'pos'       => isset($element['pos']) ? $element['pos'] : 0,
                'name'      => false,
                'duration'  => Tool_Numerics::duration_to_string($element['ticks']),
                'mode'      => __(Tool_Modes::get_mode_by_id($element['board'])),
                'players'   => [[
                    'name'  => isset($element['name']) ? $element['name'] : Model_Euser::name_by_id($element['uid']),
                    'id'    => $element['uid'],
                    'job'   => __(Tool_Modes::get_job_by_id($element['job'])),
                    'life'  => Tool_Numerics::duration_to_string($element['ticks']),
                    'score' => $element['points']
                ]]
            ];
        }, $lists);
    }

    public function japi_single() {
        $season = $this->request->current()->post('season');
        $mode = $this->request->current()->post('mode');
        $time = $this->request->current()->post('time');
        $offset = $this->request->current()->post('offset');
        $length = $this->request->current()->post('length');

        if (!isset($season, $mode, $time, $offset, $length))
            return $this->error(\grge\E_HTTP_REQUEST_INCOMPLETE);

        $ranks = $this->get_ranking_data_sp($count, $season, $mode, null, $time, null, $offset, $length);
        $this->render([
            'games' => $count,
            'ranking' => $this->convert_data($ranks)
        ]);

        return true;
    }

    public function japi_multi() {
        $season = $this->request->current()->post('season');
        $mode = $this->request->current()->post('mode');
        $offset = $this->request->current()->post('offset');
        $length = $this->request->current()->post('length');

        if (!isset($season, $mode, $offset, $length))
            return $this->error(\grge\E_HTTP_REQUEST_INCOMPLETE);

        $ranks = $this->get_ranking_data_mp($count, $season, $mode, $offset, $length);
        $this->render([
            'games' => $count,
            'ranking' => $this->convert_data($ranks, true)
        ]);

        return true;
    }

    public function japi_soul() {
        $season = $this->request->current()->post('season');
        $uid = (int)$this->request->current()->post('uid');

        if (!$uid) return;

        $cache = [];

        // Single Player
        $full = DB::select()->from('ranking')->where('season','=',$season)->where('board','IN', Tool_Gamemodes::get_singleplayer_modes())->order_by('points','DESC')->execute()->as_array();
        foreach ($full as $entry) {
            $s = "m{$entry['board']}_f{$entry['flow']}";
            if (!isset($cache[$s])) $cache[$s] = [];
            $entry['pos'] = count($cache[$s]) + 1;
            $cache[$s][] = $entry;
        }

        $finalcache = [];
        foreach ($cache as $modecache)
            $finalcache = array_merge($finalcache,array_filter($modecache, function($e) use ($uid) {return $e['uid'] == $uid;}));

        usort($finalcache, function($a,$b) {return $a['pos'] == $b['pos'] ? ($b['points'] - $a['points']) : ($a['pos'] - $b['pos']);});

        $ret = [];
        $a = 0;
        foreach ($finalcache as $f) {
            if ($a >= 10 && $f['pos'] >= 11)
                break;

            $ret[] = $f;
            $a++;
        }

        $this->render([
            'ranking' => count($ret) ? $this->convert_data($ret) : false,
        ]);
    }

    public function japi_search() {
        $query = $this->request->current()->post('query');
        if (!$query || strlen($query) < 3) return;

        $this->render([
            'users' => DB::select(['uid','id'],'name')->from('users')->where('name','LIKE',"%{$query}%")->execute()->as_array()
        ]);
    }

    public function action_lists() {

        $converter = function($meta) {
            return __($meta['name']);
        };

        $this->add_widget(View::factory('pages/ranking')
            ->set('season', Kohana::$config->load('server.season'))
            ->set('sp_modes', array_map($converter, Tool_Modes::config_get_modes('single')))
            ->set('mp_modes', array_map($converter, Tool_Modes::config_get_modes(['multi_auto','multi_custom'])))
            ->render()
        );
        $this->render();
    }

    public function action_soul() {
        /** @global Model_Euser $user */
        global $user;

        $converter = function($meta) {
            return __($meta['name']);
        };

        // Search user
        $uid = $this->request->param('id', $user->uid());
        if (!($name = Model_Euser::name_by_id($uid))) {
            $this->add_widget(View::factory('pages/notfound')
                ->set('uri', "ranking/soul/$uid")
                ->render()
            );
            return $this->render();
        }

        // Get achievements
        $achievements = DB::select('aid', array(DB::expr('SUM(`value`)'), 'value'))->from('achievements')->where('uid', '=', $uid)->group_by('aid')->execute()->as_array();
        uasort($achievements, function($a, $b) {return (Model_Achievement::points_aid($b['aid']) != Model_Achievement::points_aid($a['aid'])) ? (Model_Achievement::points_aid($b['aid']) - Model_Achievement::points_aid($a['aid'])) : $b['aid'] - $a['aid'];});

        // Get Ranks
        $spoints = Model_Euser::get_soulpoints($uid, null, null, false);
        list($srank, $next_srank) = Model_Euser::group_soulpoints($spoints);

        $kpoints = min(100,max(-100,Model_User::get_karma($uid)))/100;
        $krank = Model_User::group_karma($kpoints);

        $apoints = 0;
        foreach ($achievements as &$achievement) {
            $achievement = [
                'id' => $achievement['aid'],
                'name' => Model_Achievement::decode_aid($achievement['aid']),
                'icon' => "{$achievement['aid']}.gif",
                'class' => Model_Achievement::class_aid($achievement['aid']),
                'points' => Model_Achievement::points_aid($achievement['aid']),
                'count' => $achievement['value'],
            ];
            $apoints += $achievement['count'] * $achievement['points'];
        }


        $this->add_widget(View::factory('pages/soul')
            ->set('season', Kohana::$config->load('server.season'))
            ->set('own_soul', $uid == $user->uid())
            ->set('soul_owner', $name)
            ->set('soul_id', $uid)
            ->set('avatar', Model_Euser::avatar_by_id($uid))
            ->set('points_soul', $spoints)
            ->set('points_ach', $apoints)
            ->set('points_karma', $kpoints)
            ->set('rank_soul', $srank)
            ->set('rank_karma', $krank)
            ->set('achievements', $achievements)

            ->render()
        );
        $this->render();
    }
}