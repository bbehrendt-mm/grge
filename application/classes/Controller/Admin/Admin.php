<?php defined('SYSPATH') or die('No direct script access.');

abstract class Controller_Admin_Admin extends Controller {

    protected static $force_login = true;
    protected static $force_admin = true;
    protected static $allow_skip_login = false;
    protected static $auto_require = [];

    protected static $admin_data = null;

    protected static function priv_get($user, $pw = null) {
        if (static::$admin_data === null || $pw) {
            $data = DB::select('relation','data')->from('user_flags')->where('user', '=', $user)->execute()->as_array();
            $enable = ($pw == null);
            $tmp = [];
            foreach ($data as $row) {
                switch ($row['relation']) {
                    case 'ALLOW':
                        if (!isset($tmp[$row['data']]))
                            $tmp[$row['data']] = true;
                        break;
                    case 'DENY':
                        $tmp[$row['data']] = false;
                        break;
                    case 'DISABLE':
                        $enable = false;
                        break(2);
                    case 'LOGIN':
                        $enable = $enable || ($row['data'] == hash('sha256', $pw, false));
                        break;
                }
            }
            static::$admin_data = $enable ? $tmp : false;
            return $enable;
        } else return (bool)static::$admin_data;
    }

    /**
     * This function will return true if the current user possesses any of the given permissions AND is denied none of them, or if he has the ROOT permission. If no arguments are given, it will return true.
     * @param String $args,...
     * @return bool
     */
    protected static function priv_allow_any($args) {
        if (!static::$admin_data) return false;

        if (!is_array($args))
            $args = func_get_args();

        if (!$args)
            return true;

        if (!static::$admin_data)
            return false;
        elseif (isset(static::$admin_data['ROOT']) && static::$admin_data['ROOT'])
            return true;
        else foreach ($args as $arg)
            if (isset(static::$admin_data[$arg]) && static::$admin_data[$arg])
                return true;
            elseif (isset(static::$admin_data[$arg]) && !static::$admin_data[$arg])
                return false;
        return true;
    }

    /**
     * This function will return true if the current user possesses all of the given permissions or if he has the ROOT permission. If no arguments are given, it will return true.
     * @param String $args,...
     * @return bool
     */
    protected static function priv_allow_all($args) {
        if (!static::$admin_data) return false;

        if (!is_array($args))
            $args = func_get_args();

        if (!$args)
            return true;

        if (!static::$admin_data)
            return false;
        elseif (isset(static::$admin_data['ROOT']) && static::$admin_data['ROOT'])
            return true;
        else foreach ($args as $arg)
            if (!isset(static::$admin_data[$arg]) || !static::$admin_data[$arg])
                return false;
        return true;
    }

    public function admin_status_get($refresh = 2) {

        $admin_state = $this->session->get('admin',null);

        if (!$admin_state || !is_array($admin_state) || !isset($admin_state['ip']) ||
            !isset($admin_state['client']) || !isset($admin_state['expires']) || $admin_state['expires'] < time() ||
            $admin_state['ip'] != $_SERVER['REMOTE_ADDR'] || $admin_state['client'] != $_SERVER['HTTP_USER_AGENT'])
        {
            $this->admin_status_revoke();
            return 0;
        } else {
            if (strtotime("+$refresh minutes") > $admin_state['expires'])
                $this->admin_status_set(2);
            return max($admin_state['expires'], strtotime("+$refresh minutes")) - time();
        }
    }

    public function admin_status_set($duration = 10) {
        $this->session->set('admin', [
            'ip' => $_SERVER['REMOTE_ADDR'],
            'client' => $_SERVER['HTTP_USER_AGENT'],
            'expires' => strtotime("+$duration minutes")
        ]);
    }

    public function admin_status_revoke() {
        $this->session->delete('admin');
    }

    protected function force_admin() {
        Error::i();
        if (!(static::$allow_skip_login || $this->admin_status_get()) || !static::priv_allow_all(static::$auto_require)) {
            if (!$this->is_ajax_request())
                // Output error message as string
                die(Error::m(\grge\E_SERVER_ACCESS_DENIED));
            else {
                // Create JSOn error output, then stop the action from being executed by redirecting to noaction
                $this->error(\grge\E_SERVER_ACCESS_DENIED);
                $this->request->action('noaction');
            }
        }
    }

    public function before() {
        parent::before();

        //Check admin privileges
        if (Globals::hasCurrentUser() && static::$force_admin) {
            static::priv_get(Globals::CurrentUser()->uid());
            $this->force_admin();
        }
    }
}