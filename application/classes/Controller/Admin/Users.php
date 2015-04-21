<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Admin_Users extends Controller_Admin_Admin {

    protected static $auto_require = ['USERLIST'];

    public function action_main() {
        $this->add_widget(View::factory('admin/users')
            ->set('permissions',['ROOT','TRANSLATE','TRANSLATE_MOD','USERLIST','WHITELIST','CHEAT'])
            ->render());

        $this->render();
    }

    public function japi_flag() {
        /** @global Model_Euser $user */
        global $user;

        $users = $this->request->post('users');
        $changes = $this->request->post('set');

        foreach ($changes as $flag => $change)
            if ($flag == 'ROOT' && (!static::priv_allow_all('ROOT') || in_array($user->uid(), $users))) {
                return $this->render([
                    'success' => 0
                ]);
            }

        //Clear
        DB::delete('user_flags')->where('user', 'IN', $users)->and_where('relation', 'IN', ['ALLOW','DENY'])->and_where('data','IN',array_keys($changes))->execute();

        foreach ($users as $this_user)
            foreach ($changes as $flag => $change)
                if ($change <> 0)
                    DB::insert('user_flags', ['user','relation','data'])->values([$this_user, $change > 0 ? 'ALLOW' : 'DENY', $flag])->execute();

        return $this->render([
            'success' => 1
        ]);
    }

    public function japi_info() {
        $user = $this->request->post('id');

        $flags_a = $flags_d = [];
        foreach (DB::select('data','relation')->from('user_flags')->where('user','=',$user)->and_where('relation','IN',['ALLOW','DENY'])->execute()->as_array() as $flag)
            if ($flag['relation'] == 'ALLOW') $flags_a[$flag['data']] = true;
            else $flags_d[$flag['data']] = true;

        return $this->render([
            'flags' => [
                'allowed' => array_keys($flags_a),
                'denied' => array_keys($flags_d)
            ],
            'admin' => [
                'access' => (count(DB::select('relation')->from('user_flags')->where('user','=',$user)->and_where('relation','=','LOGIN')->execute()->as_array()) > 0),
                'disabled' => (count(DB::select('relation')->from('user_flags')->where('user','=',$user)->and_where('relation','=','DISABLED')->execute()->as_array()) > 0)
            ]
        ]);
    }

    public function japi_password() {
        /** @global Model_Euser $user */
        global $user;

        $users = $this->request->post('users');
        $to = $this->request->post('set');

        // Prevent user from resetting own password
        if (in_array($user->uid(), $users) && !$to)
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

    public function japi_search() {
        $query = explode(':', $this->request->post('query'));
        list($limit,$query) = (count($query) > 1) ? $query : ['n',$query[0]];

        if (!in_array($limit,['i','n']))
            $limit = 'n';

        $result = DB::select('uid','name')->from('users');
        switch($limit) {
            case 'i':
                $result->where('uid','=',(int)$query);
                break;
            case 'n': default:
                $result->where('name','LIKE',"%{$query}%");
                break;
        }
        $result = $result->execute()->as_array();

        foreach ($result as &$entry) {
            $entry['auth'] = [];
            foreach (Model_Auth_Interface::get_all_providers($entry['uid']) as $provider => $variables)
                /** @var Model_Auth_Interface $provider */
                $entry['auth'][$provider::get_service_name()] = [$variables['rid'],$variables['var1'],$variables['var2']];
        }


        $this->render([
            'list' => $result
        ]);
    }
}