<?php defined('SYSPATH') or die('No direct script access.');

abstract class Controller_Admin_Admin extends Controller {

    protected static $force_login = true;
    protected static $force_admin = true;

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
        if (!$this->admin_status_get()) {
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
        if (static::$force_admin)
            $this->force_admin();
    }
}