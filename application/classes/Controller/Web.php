<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Web extends Controller {

    protected static $force_ajax = false;

    public function action_framework() {
        $this->response->body(View::factory('framework'));
    }

    public function action_body() {
        $this->force_ajax();
        $this->add_widget(':body', View::factory('body')->render());
        $this->render();
    }

}