<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Admin_Users extends Controller_Admin_Admin {

    protected static $auto_require = ['USERLIST'];

    public function action_main(): void
    {
        //Cache achievements
        $ach = [];
        foreach ((new ReflectionClass('Model_Achievement'))->getConstants() as $aid)
            $ach[$aid] = Model_Achievement::decode_aid($aid);

        $this->add_widget(View::factory('admin/users')
            ->set('permissions',['ROOT','TRANSLATE','TRANSLATE_MOD','USERLIST','GAMELIST','WHITELIST','CHEAT','RANKING','WIKI','LOGVIEW'])
            ->set('achievements', $ach)
            //->set('achievements', [1 => 'a', 2 => "Gnadenstoß "])
            ->render());

        $this->render();
    }

    public function japi_achievements(): bool
    {
        $users = self::post('users');
        $aid = (int)self::post('aid');
        $count = (int)self::post('count');
        if (!$users || !$aid || !$count || !is_array($users))
            return $this->render([
                'success' => 0
            ]);

        foreach ($users as $user) {
            $local_count = $count;
            $res = DB::select('value')->from('achievements')->where('gameid', '=', 0)->and_where('uid', '=', $user)->and_where('aid', '=', $aid)->execute()->as_array();
            if (count($res) > 0) {
                $local_count += (int)$res[0]['value'];
                DB::delete('achievements')->where('gameid', '=', 0)->and_where('uid', '=', $user)->and_where('aid', '=', $aid)->execute();
            }
            if ($local_count > 0)
                DB::insert('achievements', ['uid', 'gameid', 'aid', 'value'])->values([$user, 0, $aid, $local_count])->execute();
        }

        return $this->render([
            'success' => 1
        ]);
    }

    public function japi_flag(): bool
    {
        $users = self::post('users');
        $changes = self::post('set');

        foreach ($changes as $flag => $change)
            if ($flag === 'ROOT' && (!static::priv_allow_all('ROOT') || in_array(
                        Globals::CurrentUserF()->uid(), $users, true
                    ))) {
                return $this->render([
                    'success' => 0
                ]);
            }

        // Reset
        DB::update('users')->set(['session' => null])->where('uid','IN',$users)->execute();

        //Clear
        DB::delete('user_flags')->where('user', 'IN', $users)->and_where('relation', 'IN', ['ALLOW','DENY'])->and_where('data','IN',array_keys($changes))->execute();

        foreach ($users as $this_user)
            foreach ($changes as $flag => $change)
                if ($change !== 0)
                    DB::insert('user_flags', ['user','relation','data'])->values([$this_user, $change > 0 ? 'ALLOW' : 'DENY', $flag])->execute();

        return $this->render([
            'success' => 1
        ]);
    }

    public function japi_info(): bool
    {
        $user = self::post('id');

        $flags_a = $flags_d = [];
        foreach (DB::select('data','relation')->from('user_flags')->where('user','=',$user)->and_where('relation','IN',['ALLOW','DENY'])->execute()->as_array() as $flag)
            if ($flag['relation'] === 'ALLOW') $flags_a[$flag['data']] = true;
            else $flags_d[$flag['data']] = true;

        return $this->render([
            'flags' => [
                'allowed' => array_keys($flags_a),
                'denied' => array_keys($flags_d)
            ],
            'admin' => [
                'access' => count(DB::select('relation')->from('user_flags')->where('user','=',$user)->and_where('relation','=','LOGIN')->execute()->as_array()) > 0,
                'disabled' => count(DB::select('relation')->from('user_flags')->where('user','=',$user)->and_where('relation','=','DISABLED')->execute()->as_array()) > 0
            ]
        ]);
    }

    public function japi_password(): bool
    {
        $users = self::post('users');
        $to = self::post('set');

        // Prevent user from resetting own password
        if (!$to && in_array(Globals::CurrentUserF()->uid(), $users, true))
            return $this->render([
                'success' => 0
            ]);

        //Clear
        DB::delete('user_flags')->where('user', 'IN', $users)->and_where('relation', '=', 'LOGIN')->execute();

        if ($to) foreach ($users as $this_user)
            DB::insert('user_flags', ['user','relation','data'])->values([$this_user,'LOGIN', hash('sha256', $to, false)])->execute();

        return $this->render([
            'success' => 1
        ]);
    }

    public function japi_search(): void
    {
        $query = explode(':', self::post('query'));
        [$limit,$query] = (count($query) > 1) ? $query : ['n', $query[0]];

        if (!in_array($limit,['i','n','r']))
            $limit = 'n';

        $result = DB::select('uid','name','flags.access')->from('users')->join([DB::select('user',['relation','access'])->from('user_flags')->where('data','=','WHITELIST'),'flags'],'LEFT')->on('users.uid','=','flags.user');
        switch($limit) {
            case 'i':
                $result->where('uid','=',(int)$query);
                break;
            case 'r':
                $result->where('uid','IN',DB::select(['zvid','uid'])->distinct(true)->from('profiles_xref')->where('rid','=',(int)$query));
                break;
            case 'n': default:
                $result->where('name','LIKE',"%{$query}%");
                break;
        }
        $result = $result->execute()->as_array();

        foreach ($result as &$entry) {
            $entry['auth'] = [];
            $entry['access'] = $entry['access'] ? ($entry['access'] === 'ALLOW' ? 1 : -1) : 0;
            foreach (Model_Auth_Interface::get_all_providers($entry['uid']) as $provider => $variables)
                /** @var Model_Auth_Interface $provider */
                $entry['auth'][$provider::get_service_name()] = [$variables['rid'],$variables['var1'],$variables['var2']];
        }
        unset($entry);

        $this->render([
            'list' => $result
        ]);
    }
}