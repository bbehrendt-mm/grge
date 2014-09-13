<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Lobby extends Controller {

    protected static $force_login = true;

    public function action_main() {
        $this->add_widget('main-menu',View::factory('menus/logout')->render());
        $this->add_widget(View::factory('pages/noview')
            ->render());
        $this->render();
    }

}