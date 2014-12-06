<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Admin_Main extends Controller_Admin_Admin {

    public function action_main() {
        /** @global Model_User $user */
        global $user;

        $this->add_widget(View::factory('admin/main')
            ->set('user', $user->name())
            ->set('duration', $this->admin_status_get(0))
            ->set('expires', date('r',$this->admin_status_get(0) + time()))
            ->render());

        $this->render();
    }

}