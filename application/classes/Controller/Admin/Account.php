<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Admin_Account extends Controller_Admin_Admin {

    protected static $force_admin = false;

    public function action_login() {
        /** @global Model_Euser $user */
        global $user;

        if ($this->admin_status_get(0)) {
            $this->redirect(URL::site('admin/main', 'http'));
            return;
        }

        $this->add_widget(View::factory('admin/login')
            ->set('user', $user->name())
            ->render());

        $this->render();
    }

    public function japi_login() {
        /** @global Model_Euser $user */
        global $user;

        $pw = $this->post('password');
        if (!$pw)
            return $this->error(\grge\E_SERVER_LOGIN_REJECTED);

        if (static::priv_get($user->uid(), $pw))
            $this->admin_status_set(Kohana::$environment == Kohana::PRODUCTION ? 30 : 120);

        if (!$this->admin_status_get(0))
            return $this->error(\grge\E_SERVER_LOGIN_REJECTED);

        return $this->render([
            'redirect' => 'admin/main',
            'duration' => $this->admin_status_get(0)
        ]);
    }

    public function japi_logout() {
        $this->admin_status_revoke();
        $this->render([
            'redirect' => 'lobby/main',
        ]);
    }
}