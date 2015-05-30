<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Web extends Controller {

    protected static $force_ajax = false;

    public function action_framework() {
        $js = ['jquery.min.js'];
        $css = [];
        foreach (scandir(APPPATH . 'assets/js') as $f) if (!in_array($f, ['.','..','jquery.min.js'])) $js[] = $f;
        foreach (scandir(APPPATH . 'assets/css') as $f) if (!in_array($f, ['.','..'])) $css[] = $f;

        $sid = $this->request->post('vcsid') ? $this->request->post('vcsid') : $this->session->id();
        $this->response->body(View::factory('framework')->set('js',$js)->set('css',$css)->set('sid', $sid));
    }

    private function deploy_core() {
        $buffer = '';
        $version = Kohana::$config->load('build.version');
        foreach (scandir(APPPATH . 'views/core/') as $f)
            if (!in_array($f, ['.','..','doc.js.php']))
                $buffer .= JView::factory('core/' . str_replace('.php','',$f))->set('version_data', $version);
        return $buffer;
    }

    private function debug_core() {
        I18n::set_readonly_flag();
        $buffer = '';
        $version = Kohana::$config->load('build.version');
        foreach (scandir(APPPATH . 'views/core/') as $f)
            if (!in_array($f, ['.','..']))
                $buffer .= JView::factory('core/' . str_replace('.php','',$f))->disable_compression()->set('version_data', $version);
        return $buffer;
    }

    public function action_core() {
        $this->response->headers('Content-Type', 'application/javascript; charset=utf-8');

        $path = $this->request->param('id');
        if (!$path || $path == 'deploy')
            $this->response->body($this->deploy_core());
        elseif ($path == 'debug' && Kohana::$environment === Kohana::DEVELOPMENT)
            $this->response->body($this->debug_core());
    }

    public function action_body() {
        $this->force_ajax();
        $this->add_widget(':body', View::factory('body')->render());
        $this->modify_current_url('');
        $this->render(null, true);
    }
}