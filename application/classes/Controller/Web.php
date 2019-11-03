<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Web extends Controller {

    protected static $force_ajax = false;
    protected static $override_cache_control = true;

    protected static $allow_etag_cache = true;

    public function action_skin(): void
    {
        $skin = $this->request->param('skin');
        if ($skin === 'auto') {
            setcookie('skin', '', 0, URL::base());
            setcookie('skin_cst', '', 0, URL::base());
        } elseif ($skin) {
            setcookie('skin', $skin, 0, URL::base());
            setcookie('skin_cst', '1', 0, URL::base());
        }
        $this->response->body(View::factory('redirect')->set('url',URL::base())->set('path','dev/null')->set('sid', $this->session->id()));
        $this->request->action('noaction');
    }

    public function action_save(): void
    {
        $opt = $this->request->param('opt');
        if      (in_array(strtolower($opt), ['1','on','yes'])) $opt = 1;
        else if (in_array(strtolower($opt), ['0','off','no'])) $opt = 0;
        else {
            $this->response->body(View::factory('redirect')->set('url',URL::base())->set('path','dev/null')->set('sid', $this->session->id()));
            $this->request->action('noaction');
        }

        if ($opt === 0)
            setcookie('save-data', '', 0, URL::base());
        else
            setcookie('save-data', '1', 0, URL::base());

        $this->response->body(View::factory('redirect')->set('url',URL::base())->set('path','dev/null')->set('sid', $this->session->id()));
        $this->request->action('noaction');
    }

    public function action_key(): void
    {
        if (!Globals::CurrentUserF()) {
            $this->response->body('Not logged in!');
            return;
        }

        $entries = [];
        foreach (Model_Auth_Interface::get_all_providers(Globals::CurrentUserF()->uid()) as $provider => $variables)
            /** @var Model_Auth_Interface $provider */
            $entries[$provider::get_service_name()] = [$variables['var1'],$variables['var2']];

        $ret = 'Stored login keys for '
            . Globals::CurrentUserF()->name() . '.<br /><br />';
        foreach ($entries as $name => $line)
            $ret .= "<b>$name</b> <i>{$line[0]}</i> <i>{$line[1]}</i><br />";

        if (!$entries) $ret = 'None!';
        $this->response->body($ret);
    }

    public function action_framework(): void
    {
        $force_https = Kohana::$config->load('server.io.security.strict_https');

        if ($force_https && strpos($this->request->url(true), 'https://') !== 0) {
            $target_url = $this->request->url('https');
            if (substr($target_url, -1) === '/') $target_url = substr($target_url, 0, -1);
            $target_url .= Url::query();
            $target_post = $this->request->post();
            if (!isset($target_post['ref'])) $target_post['ref'] = $this->request->referrer() ?: null;
            $this->response->body(
                View::factory('redirect')
                    ->set('url', $target_url)
                    ->set('path', null)
                    ->set('others', $target_post)
            );

            return;

        }

        $js = ['jquery.min.js'];
        $css = [];
        foreach (scandir(APPPATH . 'assets/js', SCANDIR_SORT_ASCENDING) as $f) if (!in_array($f, ['.','..','jquery.min.js'])) $js[] = $f;
        foreach (scandir(APPPATH . 'assets/css', SCANDIR_SORT_ASCENDING) as $f) if (!in_array($f, ['.','..'])) $css[] = $f;

        $sid = self::post('vcsid') ?: $this->session->id();
        //$this->response->headers('Content-Security-Policy', "connect-src 'self';");
        $this->response->body(View::factory('framework')->set('js',$js)->set('css',$css)->set('sid', $sid)->set('dev', Kohana::$environment === Kohana::DEVELOPMENT));
    }

    private function compile_js_module($name, $debug = false, $base_module = []): string
    {
        if ($debug)
            I18n::set_readonly_flag();
        $buffer = '';
        $version = Kohana::$config->load('build.version');

        foreach ($base_module as $mod) {
            $jv = JView::factory("scripts/base/$mod.js")->set('version_data', $version);
            $buffer .= $debug ? $jv->disable_compression() : $jv;
        }

        foreach (scandir(APPPATH . "views/scripts/$name/", SCANDIR_SORT_ASCENDING) as $f)
            if (!in_array($f, ['.','..'])) {
                $jv = JView::factory("scripts/$name/" . str_replace('.php','',$f))->set('version_data', $version);
                $buffer .= $debug ? $jv->disable_compression() : $jv;
            }

        return $buffer;
    }

    private function modscript($name, $base = []): void
    {
        $this->response->headers('Content-Type', 'application/javascript; charset=utf-8');

        $path = $this->request->param('id');
        if (!$path || $path === 'deploy')
            $this->response->body($this->compile_js_module($name, false, $base));
        elseif ($path === 'debug' && Kohana::$environment === Kohana::DEVELOPMENT)
            $this->response->body($this->compile_js_module($name, true, $base));
    }

    public function action_core(): void
    {
        $this->modscript('core');
    }

    public function action_battle(): void
    {
        $this->modscript('battle', ['canvasModule']);
    }

    public function action_map(): void
    {
        $this->modscript('map', ['canvasModule']);
    }

    public function action_avatar(): void {
        $key = $this->request->param('id');
        $data = DB::select('local_avatar','local_avatar_name')->from('user_data')->where('access','=', $key)->and_where('local_avatar_name', 'IS NOT', null)->execute()->as_array();

        if (sizeof($data) !== 1) {
            $this->response->status(404);
            die('Not found.');
        }

        $data = $data[0];

        [$ts,$type] = explode('.', $data['local_avatar_name']);

        $etag = md5($ts);
        $not_modified = $this->request->headers( 'If-None-Match' ) === $etag;

        if ($not_modified) {
            $this->response->status(304);
            $this->response->headers('Vary','Accept-Encoding');
            $this->response->body('');
            return;
        }

        $this->response->headers([
            'ETag' => $etag,
            'Content-Length' => strlen($data['local_avatar']),
            'Content-Type' => "image/{$type}",
            'Cache-Control' => 'must-revalidate',
            'Pragma' => 'no-cache',
            'Vary' => 'Accept-Encoding'
         ]);

        $this->response->body( $data['local_avatar'] );

    }

    public function action_body(): void
    {
        $this->force_ajax();
        
        $season = (int)Kohana::$config->load('server.season');
        $title = Tool_System::getSeasonTitle($season);

        $version_data = Kohana::$config->load('build.version');
        $beta = $version_data['stage'] < 3;

        $evs = Tool_Events::current_events();
        $ev_name = count($evs) === 1 ? $evs[0]::name() : null;

        $this->add_widget(':body', View::factory('body')
            ->set('season', Kohana::$config->load('server.season'))
            ->set('title', $title)
            ->set('beta', $beta)
            ->set('event', $ev_name)
            ->set('version', "GRGE {$version_data['major']}.{$version_data['minor']}.{$version_data['service']}-{$version_data['maintenance']}-{$version_data['stage']}-{$version_data['build']} ({$version_data['date']})")
            ->render());
        $this->modify_current_url('');
        $this->render(null, true);
    }
}