<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Admin_Main extends Controller_Admin_Admin {

    public function action_main() {
        $this->add_widget(View::factory('admin/main')
            ->set('user', Globals::CurrentUser()->name())
            ->set('duration', $this->admin_status_get(0))
            ->set('expires', date('r',$this->admin_status_get(0) + time()))

            ->set('allow_translate', static::priv_allow_all('TRANSLATE'))
            ->set('allow_userlist', static::priv_allow_all('USERLIST'))
            ->set('allow_gamelist', static::priv_allow_all('GAMELIST'))
            ->set('allow_ranking', static::priv_allow_all('RANKING'))
            ->set('allow_wiki', static::priv_allow_all('WIKI'))
            ->set('allow_logs', static::priv_allow_all('LOGVIEW'))
            ->render());

        $this->render();
    }

}