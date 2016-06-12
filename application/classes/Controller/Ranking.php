<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Ranking extends Controller {

    protected static $force_login = true;
    protected static $menu = 'logout';

    private function get_ranking_data_sp(&$raw_count, $season = null, $mode = null, $job = null, $flow = null, $player = null, $offset = null, $count = null) {
        $base_query = DB::select('season', 'gameid', 'points', 'ticks', 'job', 'board', 'start', 'end', 'name', 'ranking.uid', 'ranking.flow')->from('ranking')->join('users')->on('ranking.uid', '=', 'users.uid')->where('users.uid', 'NOT IN', DB::select('user')->from('user_flags')->where('relation','=','DENY')->where('data','=','WHITELIST'));

        if ($season !== null) $base_query->where('season', '=', $season);
        if ($mode !== null) $base_query->where('board', '=', $mode); else $base_query->where('board', 'IN', Tool_Gamemodes::get_singleplayer_modes());
        if ($job !== null) $base_query->where('job', '=', $mode);
        if ($flow !== null) $base_query->where('flow', '=', $flow);
        if ($player !== null) $base_query->where('users.uid', '=', $player);

        $data = $base_query->order_by('points', 'DESC')->order_by('ticks', 'DESC')->execute()->as_array();
        $raw_count = count($data);

        $p = -1; $skip = 0;
        foreach ($data as $pos => &$entry) {
            if ($entry['points'] == $p)
                $skip++;
            else {
                $skip = 0;
                $p = $entry['points'];
            }

            $entry['pos'] = ($pos + 1) - $skip;
        }

        return array_slice($data, $offset === null ? 0 : $offset, $count, true);
    }

    private function get_ranking_data_mp(&$raw_count, $season = null, $mode = null, $offset = null, $count = null) {
        $base_query = DB::select('ranking_mp.season','ranking_mp.gameid', ['ranking_mp.board','board'], 'ranking_mp.name', 'ranking_mp.points', 'users.uid', array('ranking.ticks', 'pticks'),array('ranking.points', 'ppoints'), array('ranking.job', 'job'), array('users.name', 'player'))->from('ranking_mp')->join('ranking', 'LEFT')->on('ranking_mp.gameid', '=', 'ranking.gameid')->on('ranking_mp.season', '=', 'ranking.season')->join('users', 'LEFT')->on('ranking.uid', '=', 'users.uid');

        if ($season !== null) $base_query->where('ranking_mp.season', '=', $season);
        if ($mode !== null) $base_query->where('ranking_mp.board', '=', $mode); else $base_query->where('ranking_mp.board', 'IN', Tool_Gamemodes::get_multiplayer_modes());

        $data = $base_query->order_by('ranking_mp.points', 'DESC')->execute()->as_array();

        $ranks = [];
        foreach ($data as $line) {
            $adr = "{$line['season']}-{$line['gameid']}";
            if (!isset($ranks[$adr]))
                $ranks[$adr] = [];

            $ranks[$adr][] = $line;
        }
        $ranks = array_values($ranks);

        $raw_count = count($ranks);

        $p = -1; $skip = 0;
        foreach ($ranks as $pos => &$r_line)
            foreach ($r_line as &$entry) {
                if ($entry['points'] == $p)
                    $skip++;
                else {
                    $skip = 0;
                    $p = $entry['points'];
                }

                $entry['pos'] = ($pos + 1) - $skip;
            }

        return array_slice($ranks, $offset === null ? 0 : $offset, $count, true);
    }

    private function convert_data($lists, $is_mp = false) {
        return $is_mp ? array_map(function($element) {
            /** @global Model_Euser $user */
            global $user;

            $tmp = [
                'season'    => $element[0]['season'],
                'id'        => $element[0]['gameid'],
                'score'     => $element[0]['points'],
                'pos'       => isset($element[0]['pos']) ? $element[0]['pos'] : 0,
                'flow'      => 1,
                'name'      => $element[0]['name'],
                'duration'  => false,
                'mode'      => __(Tool_Modes::get_mode_by_id($element[0]['board'])),
                'players'   => []
            ];

            if ($element[0]['uid'])
                foreach ($element as $sub) {
                    if ($sub['uid'] == $user->uid())
                        $tmp['mark'] = true;

                    $tmp['players'][(int)$sub['uid']] = [
                        'name' => $sub['player'],
                        'id' => $sub['uid'],
                        'job' => __(Tool_Modes::get_job_by_id($sub['job'])),
                        'life' => Tool_Numerics::duration_to_string($sub['pticks']),
                        'score' => $sub['ppoints']
                    ];
                }

            return $tmp;
        }, $lists) : array_map(function($element) {
            /** @global Model_Euser $user */
            global $user;

            return [
                'season'    => $element['season'],
                'id'        => $element['gameid'],
                'score'     => $element['points'],
                'pos'       => isset($element['pos']) ? $element['pos'] : 0,
                'flow'      => isset($element['flow']) ? $element['flow'] : false,
                'name'      => false,
                'duration'  => Tool_Numerics::duration_to_string($element['ticks']),
                'mode'      => __(Tool_Modes::get_mode_by_id($element['board'])),
                'mark'      => $element['uid'] == $user->uid(),
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
        $cache_m = [];

        // Single Player
        $p = []; $skip = [];
        foreach ($this->get_ranking_data_sp($r,$season) as $entry) {
            $s = "m{$entry['board']}_f{$entry['flow']}";
            if (!isset($cache[$s])) $cache[$s] = [];
            if (!isset($p[$s])) $p[$s] = -1;
            if (!isset($skip[$s])) $skip[$s] = 0;

            if ($entry['points'] == $p[$s])
                $skip[$s]++;
            else {
                $skip[$s] = 0;
                $p[$s] = $entry['points'];
            }

            $entry['pos'] = count($cache[$s]) + 1 - $skip[$s];
            $cache[$s][] = $entry;
        }

        // Multi Player
        $p = []; $skip = [];
        foreach ($this->get_ranking_data_mp($r,$season) as $entry) {
            $s = "m{$entry[0]['board']}";
            if (!isset($cache_m[$s])) $cache_m[$s] = [];
            if (!isset($p[$s])) $p[$s] = -1;
            if (!isset($skip[$s])) $skip[$s] = 0;

            if ($entry[0]['points'] == $p[$s])
                $skip[$s]++;
            else {
                $skip[$s] = 0;
                $p[$s] = $entry[0]['points'];
            }

            $entry[0]['pos'] = count($cache_m[$s]) + 1 - $skip[$s];
            $cache_m[$s][] = $entry;
        }

        $finalcache = [];
        foreach ($cache as $modecache)
            $finalcache = array_merge($finalcache,array_filter($modecache, function($e) use ($uid) {return $e['uid'] == $uid;}));
        $finalcache_m = [];
        foreach ($cache_m as $modecache)
            $finalcache_m = array_merge($finalcache_m,array_filter($modecache, function($e) use ($uid) {
                foreach ($e as $es)
                    if ($es['uid'] == $uid) return true;
                return false;
            }));

        usort($finalcache, function($a,$b) {return $a['pos'] == $b['pos'] ? ($b['points'] - $a['points']) : ($a['pos'] - $b['pos']);});
        usort($finalcache_m, function($a,$b) {return $a[0]['pos'] == $b[0]['pos'] ? ($b[0]['points'] - $a[0]['points']) : ($a[0]['pos'] - $b[0]['pos']);});

        $ret = [];
        $a = 0;
        foreach ($finalcache as $f) {
            if ($a >= 10 && $f['pos'] >= 11)
                break;

            $ret[] = $f;
            $a++;
        }

        $ret_m = [];
        $a = 0;
        foreach ($finalcache_m as $f) {
            if ($a >= 10 && $f[0]['pos'] >= 11)
                break;

            $ret_m[] = $f;
            $a++;
        }

        $this->render([
            'ranking' => count($ret) ? $this->convert_data($ret) : false,
            'ranking_mp' => count($ret_m) ? $this->convert_data($ret_m, true) : false,
        ]);
    }

    public function japi_search() {
        $query = $this->request->current()->post('query');
        if (!$query || strlen($query) < 3) return;

        $this->render([
            'users' => DB::select(['uid','id'],'name','avatar')->from('users')->where('name','LIKE',"%{$query}%")->execute()->as_array()
        ]);
    }

    public function japi_achievements() {
        /** @global Model_Euser $user */
        global $user;

        $aid = (int)$this->request->current()->post('aid');
        $offset = $this->request->current()->post('offset');
        $length = $this->request->current()->post('length');


        $lists_raw = DB::select('achievements.uid','achievements.aid','users.name',[DB::expr('SUM(value)'),'value'])->from([DB::select('*')->from('achievements')->where('aid',($aid > 0) ? '=' : '>', max(0,$aid)),'achievements'])->group_by('achievements.uid')->group_by('achievements.aid')->join('users','INNER')->on('achievements.uid','=','users.uid')->where('value','>',0)->execute()->as_array();
        $lists = [];
        foreach ($lists_raw as $entry) {
            if (!isset($lists[$entry['uid']])) {
                $lists[$entry['uid']] = $entry;
                $lists[$entry['uid']]['value'] = 0;
            }
            $lists[$entry['uid']]['value'] += $entry['value'] * (($aid <= 0) ? Model_Achievement::points_aid($entry['aid']) : 1);
        }
        $num_entries = count($lists);
        usort($lists, function($a,$b) {return $b['value'] - $a['value'];});

        $user_pos = [];
        $p = -1; $skip = 0;
        foreach ($lists as $k => &$entry) {
            if ($entry['uid'] == $user->uid()) {
                $lists[$k]['mark'] = true;
                $user_pos = $entry;
                $user_pos['pos'] = $k+1;
            }

            if ($entry['value'] == $p)
                $skip++;
            else {
                $p = $entry['value'];
                $skip = 0;
            }

            $entry['pos'] = ($k + 1) - $skip;
        }

        $this->render([
            'games' => $num_entries,
            'user' => $user_pos,
            'ranking' => array_map(function($v) {
                unset($v['aid']);
                return $v;
            }, array_slice($lists, $offset, $length, true))
        ]);
    }

    public function japi_soulpoints() {
        /** @global Model_Euser $user */
        global $user;

        $offset = $this->request->current()->post('offset');
        $length = $this->request->current()->post('length');

        $lists = DB::select('ranking.uid','users.name',[DB::expr('SUM(points)'),'points'])->from('ranking')->group_by('ranking.uid')->join('users','INNER')->on('ranking.uid','=','users.uid')->where('points','>',0)->order_by('points','DESC')->execute()->as_array();
        $num_entries = count($lists);

        $user_pos = [];
        foreach ($lists as $k => $entry)
            if ($entry['uid'] == $user->uid()) {
                $lists[$k]['mark'] = true;
                $user_pos = $entry;
                $user_pos['pos'] = $k+1;
                break;
            }

        array_unshift($lists, true);
        $this->render([
            'games' => $num_entries,
            'user' => $user_pos,
            'ranking' => array_slice($lists, 1 + $offset, $length, true)
        ]);
    }

    public function action_lists() {

        $converter = function($meta) {
            return __($meta['name']);
        };

        $season = Kohana::$config->load('server.season');
        $titles = array_map(function($t) {return __($t);}, Tool_System::getSeasonTitle());

        $this->add_widget(View::factory('pages/ranking')
            ->set('season', $season)
            ->set('titles', $titles)
            ->set('sp_modes', array_map($converter, Tool_Modes::config_get_modes('single')))
            ->set('mp_modes', array_map($converter, Tool_Modes::config_get_modes(['multi_auto','multi_custom','special_multi_auto'])))
            ->render()
        );
        $this->render();
    }

    public function action_global() {
        $known_achievements = DB::select('aid',[DB::expr('SUM(value)'),'value'])->from('achievements')->group_by('aid')->where('value','>',0)->execute()->as_array('aid','value');
        $achievements = [];
        foreach ((new ReflectionClass('Model_Achievement'))->getConstants() as $aid)
            $achievements[$aid] = [
                'id' => $aid,
                'name' => Model_Achievement::decode_aid($aid),
                'points' => Model_Achievement::points_aid($aid),
                'class' => Model_Achievement::class_aid($aid),
                'count' => isset($known_achievements[$aid]) ? $known_achievements[$aid] : 0,
                'icon' => "{$aid}.gif",
            ];

        usort($achievements, function($a,$b) {return ($a['points'] == $b['points'] ? -strcmp($a['name'], $b['name']) : $b['points'] - $a['points']);});

        $preset = $this->request->param('id', 0);
        if (!Model_Achievement::is_valid($preset))
            $preset = 0;

        $this->add_widget(View::factory('pages/ranking_global')
            ->set('achievements', $achievements)
            ->set('preset', $preset)
            ->render()
        );
        $this->render();
    }

    public function action_game() {
        $data = explode('/', $this->request->param('id', ''));

        if (count($data) < 2) $data = [-1,-1];
        list($season, $gameid) = $data;

        $season = (int)$season;
        $gameid = (int)$gameid;

        if ($season < 0 || $season > (int)Kohana::$config->load('server.season') || $gameid <= 0)
            return $this->not_found();

        $entries = DB::select()->from('ranking')->where('gameid','=',$gameid)->where('season','=',$season)->execute()->as_array();
        if (!count($entries))
            return $this->not_found();

        foreach ($entries as $entry)
            if ($entry['board'] !== $entries[0]['board'])
                return $this->not_found();

        $multiplayer = in_array((int)$entries[0]['board'], Tool_Gamemodes::get_multiplayer_modes());
        if (!$multiplayer && count($entries) > 1)
            return $this->not_found();

        // Get multiplayer data
        if ($multiplayer) {
            $mp_entry = DB::select()->from('ranking_mp')->where('gameid','=',$gameid)->where('season','=',$season)->execute()->as_array();
            if (count($mp_entry) != 1) return $this->not_found();
            else $mp_entry = $mp_entry[0];

            if ($mp_entry['board'] !== $entries[0]['board']) return $this->not_found();
        } else $mp_entry = null;


        $game_start = PHP_INT_MAX;
        $game_end = 0;


        // Get user data
        $achievement_db = [];
        $users = [];
        foreach ($entries as $entry) {
            $game_start = min($game_start, (int)$entry['start']);
            $game_end = max($game_end, (int)$entry['end']);

            $user = [
                'id' => (int)$entry['uid'],
                'name' => Model_User::name_by_id((int)$entry['uid']),
                'score_sp' => (int)$entry['points'],
                'score_ap' => 0,
                'ticks' => (int)$entry['ticks'],
                'achievements' => DB::select('aid','value')->from('achievements')->where('gameid','=',$gameid)->where('season','=',$season)->where('uid','=', (int)$entry['uid'])->execute()->as_array()
            ];

            $user['achievements'] = array_map(function($e) {
                return [
                    'name' => Model_Achievement::decode_aid($e['aid']),
                    'points' => Model_Achievement::points_aid($e['aid']),
                    'class' => Model_Achievement::class_aid($e['aid']),
                    'count' => (int)$e['value'],
                    'icon' => "{$e['aid']}.gif",
                    'id' => (int)$e['aid']
                ];
            }, $user['achievements']);

            foreach ($user['achievements'] as $uae) {
                $user['score_ap'] += $uae['points'] * $uae['count'];
                $achievement_db["a{$uae['id']}"] = $uae;
            }


            usort($user['achievements'], function($a,$b) {return $b['points'] - $a['points'];});

            $users[] = $user;
        }

        usort($users, function($a, $b) {return $b['ticks'] - $a['ticks'];});

        $this->add_widget(View::factory('pages/ranking_game')
            ->set('season', $season)
            ->set('mode', Tool_Gamemodes::get_board_by_id((int)$entries[0]['board']))
            ->set('multiplayer', $multiplayer)
            ->set('score_sp', $multiplayer ? (int)$mp_entry['points'] : $users[0]['score_sp'])
            ->set('score_ap', $multiplayer ? 0 : $users[0]['score_ap'])
            ->set('from', $game_start)
            ->set('to', $game_end)
            ->set('ticks', $multiplayer ? 0 : $users[0]['ticks'])
            ->set('name', $multiplayer ? $mp_entry['name'] : null)
            ->set('players', $users)
            ->set('achievement_db', array_values($achievement_db))
            ->render()
        );
        return $this->render();
    }

    public function action_soul() {
        /** @global Model_Euser $user */
        global $user;

        // Search user
        $uid = $this->request->param('id', $user->uid());
        if (!($name = Model_Euser::name_by_id($uid)))
            return $this->not_found();

        // Get achievements
        $achievements = DB::select('aid', array(DB::expr('SUM(`value`)'), 'value'))->from('achievements')->where('uid', '=', $uid)->group_by('aid')->execute()->as_array();
        uasort($achievements, function($a, $b) {return (Model_Achievement::points_aid($b['aid']) != Model_Achievement::points_aid($a['aid'])) ? (Model_Achievement::points_aid($b['aid']) - Model_Achievement::points_aid($a['aid'])) : $b['aid'] - $a['aid'];});

        // Get Ranks
        $spoints = Model_Euser::get_soulpoints($uid, null, null);
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

        $mentor = Model_Euser::mentor_id($uid);
        if ($mentor)
            $mentor_data = [
                'name' => Model_Euser::name_by_id($mentor),
                'avatar' => Model_Euser::avatar_by_id($mentor),
                'uid' => $mentor
            ];
        else $mentor_data = $mentor;

        $pupils = Model_Euser::apprentice_id($uid);
        $pupils_data = array_map(function($auid) {
            return [
                'name' => Model_Euser::name_by_id($auid),
                'avatar' => Model_Euser::avatar_by_id($auid),
                'uid' => $auid
            ];
        }, $pupils);

        if ($uid == $user->uid())
            $mcash = ($mentor || $pupils) ? [
                'mentor' => [
                    'overall' => Model_Euser::get_mentor_braincoins($mentor, $uid),
                    'harvest' => 0
                ],
                'overall' => Model_Euser::get_mentor_braincoins($uid),
                'harvest' => Model_Euser::get_mentor_braincoins($uid, null, false)
            ] : false;
        elseif ($mentor == $user->uid())
            $mcash = [
                'mentor' => true,
                'overall' => Model_Euser::get_mentor_braincoins($user->uid(), $uid),
                'harvest' => Model_Euser::get_mentor_braincoins($user->uid(), $uid, false)
            ];
        elseif (in_array($user->uid(), $pupils))
            $mcash = [
                'mentor' => false,
                'overall' => Model_Euser::get_mentor_braincoins($uid, $user->uid()),
                'harvest' => 0
            ];
        else $mcash = false;

        $season = Kohana::$config->load('server.season');
        $titles = Tool_System::getSeasonTitle();

        $this->add_widget(View::factory('pages/soul')
            ->set('season', $season)
            ->set('titles', $titles)
            ->set('own_soul', $uid == $user->uid())
            ->set('soul_owner', $name)
            ->set('soul_id', $uid)
            ->set('avatar', Model_Euser::avatar_by_id($uid))
            ->set('points_soul', $spoints)
            ->set('points_ach', $apoints)
            ->set('points_karma', $kpoints)
            ->set('rank_soul', $srank)
            ->set('next_rank_points', $next_srank)
            ->set('rank_karma', $krank)
            ->set('achievements', $achievements)
            ->set('tables', [
                'mode' => array_map(function($a) {return ['name' => Tool_Gamemodes::get_board_by_id($a['board']), 'points' => $a['points']];}, DB::select('board', [DB::expr('SUM(points)'), 'points'])->from('ranking')->where('uid','=',$uid)->where('season', '>=', 0)->group_by('board','uid')->order_by('board', 'ASC')->execute()->as_array()),
                'job' => array_map(function($a) {return ['name' => Tool_Gamemodes::get_job_by_id($a['job']), 'points' => $a['points']];}, DB::select('job', [DB::expr('SUM(points)'), 'points'])->from('ranking')->where('uid','=',$uid)->where('season', '>=', 0)->group_by('job','uid')->order_by('job', 'ASC')->execute()->as_array())
            ])
            ->set('mentor', $mentor_data)
            ->set('pupils', $pupils_data)
            ->set('cashout', $mcash)
            ->set('mentor_ref', $uid == $user->uid() ? Model_Euser::get_mentoring_ref($uid) : false)
            ->set('allow_mentor', Model_Euser::check_mentor($user->uid(), $uid))
            ->set('gallery', Model_Combat_Handler::gallery_by_player($uid))
            ->set('url', URL::base(true))

            ->render()
        );
        return $this->render();
    }
}