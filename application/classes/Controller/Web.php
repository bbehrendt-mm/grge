<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Web extends Controller {

    protected static $force_ajax = false;
    protected static $override_cache_control = true;

    protected static $allow_etag_cache = true;

    public function action_skin() {
        $skin = $this->request->param('skin');
        if ($skin == 'auto') {
            setcookie('skin', '', 0, URL::base());
            setcookie('skin_cst', '', 0, URL::base());
        } elseif ($skin) {
            setcookie('skin', $skin, 0, URL::base());
            setcookie('skin_cst', '1', 0, URL::base());
        }
        $this->response->body(View::factory('redirect')->set('url',URL::base())->set('path','')->set('sid', $this->session->id()));
        $this->request->action('noaction');
    }

    public function action_key() {
        if (!Globals::CurrentUser()) {
            $this->response->body("Not logged in!");
            return;
        }

        $entries = [];
        foreach (Model_Auth_Interface::get_all_providers(Globals::CurrentUser()->uid()) as $provider => $variables)
            /** @var Model_Auth_Interface $provider */
            $entries[$provider::get_service_name()] = [$variables['var1'],$variables['var2']];

        $ret = "Stored login keys for " . Globals::CurrentUser()->name() . ".<br /><br />";
        foreach ($entries as $name => $line)
            $ret .= "<b>$name</b> <i>{$line[0]}</i> <i>{$line[1]}</i><br />";

        if (!$entries) $ret = "None!";
        $this->response->body($ret);
    }

    public function action_framework() {
        $js = ['jquery.min.js'];
        $css = [];
        foreach (scandir(APPPATH . 'assets/js') as $f) if (!in_array($f, ['.','..','jquery.min.js'])) $js[] = $f;
        foreach (scandir(APPPATH . 'assets/css') as $f) if (!in_array($f, ['.','..'])) $css[] = $f;

        $sid = $this->post('vcsid') ? $this->post('vcsid') : $this->session->id();
        //$this->response->headers('Content-Security-Policy', "connect-src 'self';");
        $this->response->body(View::factory('framework')->set('js',$js)->set('css',$css)->set('sid', $sid)->set('dev', Kohana::$environment == Kohana::DEVELOPMENT));
    }

    private function compile_js_module($name, $debug = false, $base_module = []) {
        if ($debug)
            I18n::set_readonly_flag();
        $buffer = '';
        $version = Kohana::$config->load('build.version');

        foreach ($base_module as $mod) {
            $jv = JView::factory("scripts/base/$mod.js")->set('version_data', $version);
            $buffer .= $debug ? $jv->disable_compression() : $jv;
        }

        foreach (scandir(APPPATH . "views/scripts/$name/") as $f)
            if (!in_array($f, ['.','..'])) {
                $jv = JView::factory("scripts/$name/" . str_replace('.php','',$f))->set('version_data', $version);
                $buffer .= $debug ? $jv->disable_compression() : $jv;
            }

        return $buffer;
    }

    private function modscript($name, $base = []) {
        $this->response->headers('Content-Type', 'application/javascript; charset=utf-8');

        $path = $this->request->param('id');
        if (!$path || $path == 'deploy')
            $this->response->body($this->compile_js_module($name, false, $base));
        elseif ($path == 'debug' && Kohana::$environment === Kohana::DEVELOPMENT)
            $this->response->body($this->compile_js_module($name, true, $base));
    }

    public function action_core() {
        $this->modscript('core');
    }

    public function action_battle() {
        $this->modscript('battle', ['canvasModule']);
    }

    public function action_map() {
        $this->modscript('map', ['canvasModule']);
    }

    public function action_body() {
        $this->force_ajax();
        
        $season = (int)Kohana::$config->load('server.season');
        $title = Tool_System::getSeasonTitle($season);

        $version_data = Kohana::$config->load('build.version');
        $beta = $version_data['stage'] < 3;

        $this->add_widget(':body', View::factory('body')
            ->set('season', Kohana::$config->load('server.season'))
            ->set('title', $title)
            ->set('beta', $beta)
            ->set('event', Tool_Events::event_extended_name())
            ->set('version', "GRGE {$version_data['major']}.{$version_data['minor']}.{$version_data['service']}-{$version_data['maintenance']}-{$version_data['stage']}-{$version_data['build']} ({$version_data['date']})")
            ->render());
        $this->modify_current_url('');
        $this->render(null, true);
    }
}