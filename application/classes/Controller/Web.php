<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Web extends Controller {

    protected static $force_ajax = false;
    protected static $override_cache_control = true;

    protected static $allow_etag_cache = true;

    public function action_skin() {
        $skin = $this->request->param('skin');
        if ($skin) {
            setcookie('skin', $skin, 0, URL::base());
            setcookie('skin_cst', '1', 0, URL::base());
        }
        $this->response->body(View::factory('redirect')->set('url',URL::base())->set('path','')->set('sid', $this->session->id()));
        $this->request->action('noaction');
    }

    public function action_framework() {
        $js = ['jquery.min.js'];
        $css = [];
        foreach (scandir(APPPATH . 'assets/js') as $f) if (!in_array($f, ['.','..','jquery.min.js'])) $js[] = $f;
        foreach (scandir(APPPATH . 'assets/css') as $f) if (!in_array($f, ['.','..'])) $css[] = $f;

        $sid = $this->request->post('vcsid') ? $this->request->post('vcsid') : $this->session->id();
        $this->response->body(View::factory('framework')->set('js',$js)->set('css',$css)->set('sid', $sid));
    }

    private function compile_js_module($name, $debug = false) {
        if ($debug)
            I18n::set_readonly_flag();
        $buffer = '';
        $version = Kohana::$config->load('build.version');
        foreach (scandir(APPPATH . "views/$name/") as $f)
            if (!in_array($f, ['.','..'])) {
                $jv = JView::factory("$name/" . str_replace('.php','',$f))->set('version_data', $version);
                $buffer .= $debug ? $jv->disable_compression() : $jv;
            }

        return $buffer;
    }

    public function action_core() {
        $this->response->headers('Content-Type', 'application/javascript; charset=utf-8');

        $path = $this->request->param('id');
        if (!$path || $path == 'deploy')
            $this->response->body($this->compile_js_module('core', false));
        elseif ($path == 'debug' && Kohana::$environment === Kohana::DEVELOPMENT)
            $this->response->body($this->compile_js_module('core', true));
    }

    public function action_battle() {
        $this->response->headers('Content-Type', 'application/javascript; charset=utf-8');

        $path = $this->request->param('id');
        if (!$path || $path == 'deploy')
            $this->response->body($this->compile_js_module('battle', false));
        elseif ($path == 'debug' && Kohana::$environment === Kohana::DEVELOPMENT)
            $this->response->body($this->compile_js_module('battle', true));
    }

    public function action_body() {
        $this->force_ajax();
        $this->add_widget(':body', View::factory('body')->render());
        $this->modify_current_url('');
        $this->render(null, true);
    }
}