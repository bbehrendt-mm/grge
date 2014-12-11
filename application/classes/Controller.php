<?php defined('SYSPATH') or die('No direct script access.');

abstract class Controller extends Kohana_Controller {

    /**
     * @var Session $session
     */
    protected $session;
    protected static $force_ajax = true;
    protected static $force_login = false;
    protected static $menu = null;
    private $widgets = array();
    private $notifications = array();

    protected function is_ajax_request() {
        return ($this->request->headers('X-Requested-With') == 'XMLHttpRequest' );
    }

    protected function force_ajax() {
        if (!$this->is_ajax_request())
            die(Error::m(\grge\E_HTTP_AJAX_REQUIRED));
    }

    private function get_user_obj() {
        global $user;
        if (empty($user))
            $user = $this->session->get('user',NULL);

        return !empty($user);
    }

    private function force_login() {
        /** @global Model_Euser $user */
        global $user;

        if (!$this->get_user_obj() || !$user->valid()) {
            Session::instance()->destroy();
            unset($GLOBALS['game']);
            unset($GLOBALS['user']);

            if (!$this->is_ajax_request())
                // Output error message as string
                die(Error::m(\grge\E_SERVER_INVALID_SESSION));
            else {
                // Create JSOn error output, then stop the action from being executed by redirecting to noaction
                $this->error(\grge\E_SERVER_INVALID_SESSION);
                $this->request->action('noaction');
            }
        }
    }

    public function before() {
        //Init error class
        Error::i();

        //Check AJAX
        if (static::$force_ajax)
            $this->force_ajax();

        //Load session, perform session checks
        $this->session = Session::instance();
        if (static::$force_login)
            $this->force_login();

        //Avoid caching!
        $this->response->headers("Cache-Control: no-cache, must-revalidate");
        $this->response->headers("Expires: Sat, 26 Jul 1997 05:00:00 GMT");

        //TODO: Maintenance Mode
    }

    public function action_noaction() {}

    public function action_japi() {
        $action = $this->request->param('jaction');
        $method = "japi_{$action}";

        if (!$action)
            return $this->error(\grge\E_HTTP_REQUEST_INCOMPLETE);

        if (method_exists($this, $method))
            return $this->$method();
        else return $this->error(\grge\E_HTTP_REQUEST_INVALID);
    }

    private function render_defaults() {
        if (!isset($this->widgets['main-menu']) && static::$menu)
            $this->add_widget('main-menu', View::factory('menus/' . static::$menu)->render());
    }

    protected function add_widget($widget, $content = null) {
        if ($content === null) $this->widgets['content'] = $widget;
        else $this->widgets[$widget] = $content;
    }

    protected function add_note($type, $content = null, $title = false) {
        if ($content === null) $this->notifications[] = array('type' => '', 'content' => $title, 'title' => false);
        else $this->notifications[] = array('type' => $type, 'content' => $content, 'title' => $title);
    }

    protected function render($obj = null) {
        $this->response->headers('Content-Type', 'application/json');

        if ($obj === null) {
            $this->render_defaults();
            $obj = array();
        }

        $version_data = Kohana::$config->load('build.version');
        if (empty($obj['profiling']) && $version_data['stage'] < 3) {
            $obj['profiling'] = [
                'version' => "GRGE {$version_data['major']}.{$version_data['minor']}.{$version_data['service']}-{$version_data['stage']}-{$version_data['maintenance']}-{$version_data['build']} ({$version_data['date']})",
                'path' => $this->request->controller() . ' / ' . ($this->request->action() == 'japi' ? ($this->request->param('jaction') . ' (japi)') : $this->request->action()),
                'memory' => number_format((memory_get_peak_usage() - KOHANA_START_MEMORY) / 1024, 2).'KB',
                'time' => number_format(microtime(TRUE) - KOHANA_START_TIME, 5).'s'
            ];
        }
        if (empty($obj['content']) && !empty($this->widgets))
            $obj['content'] = $this->widgets;
        if (empty($obj['notifications']) && !empty($this->notifications))
            $obj['notifications'] = $this->notifications;

        $this->response->body(json_encode($obj, JSON_FORCE_OBJECT));
        return true;
    }

    protected function error($c, $additional_data = null) {
        $this->response->headers('Content-Type', 'application/json');
        $tmp = array('error' => array(
            'code' => $c,
            'name' => Error::r($c),
            'message' => Error::d($c),
            'details' => $additional_data,
        ));

        $this->response->body(json_encode($tmp, JSON_FORCE_OBJECT));
        return false;
    }
}