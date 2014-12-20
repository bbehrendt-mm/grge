<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Web extends Controller {

    protected static $force_ajax = false;

    public function action_framework() {
        $this->response->body(View::factory('framework'));
    }

    public function action_core() {
        $buffer = '';
        $version = Kohana::$config->load('build.version');
        foreach (scandir(APPPATH . 'views/core/') as $f)
            if (!in_array($f, ['.','..']))
                $buffer .= JView::factory('core/' . str_replace('.php','',$f))->set('version_data', $version);
        $this->response->headers('Content-Type', 'application/javascript; charset=utf-8');
        $this->response->body($buffer);
    }

    public function action_body() {
        $this->force_ajax();
        $this->add_widget(':body', View::factory('body')->render());
        $this->render();
    }

}