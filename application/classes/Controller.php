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
    private $data = array();

    /**
     * Returns true when the current request was made using AJAX calls
     * @return bool
     */
    protected function is_ajax_request() {
        return ($this->request->headers('X-Requested-With') == 'XMLHttpRequest' );
    }

    /**
     * This function will cause the script to abort when it was not called using AJAX
     */
    protected function force_ajax() {
        if (!$this->is_ajax_request())
            die(Error::m(\grge\E_HTTP_AJAX_REQUIRED));
    }

    /**
     * Binds the active user object globally. Returns true, when the object was successfully bound; otherwise false
     * @return bool
     */
    private function get_user_obj() {
        global $user;
        if (empty($user))
            $user = $this->session->get('user',NULL);

        return !empty($user);
    }

    /**
     * This function will cause the script to abort when the user is not logged in
     */
    private function force_login() {
        /** @global Model_Euser $user */
        global $user;

        // Get user object, check if it is valid
        if (!$this->get_user_obj() || !$user->valid()) {
            // If we don't have a user object, destroy the current session and unbind global registers (just to be sure)
            Session::instance()->destroy();
            unset($GLOBALS['game']);
            unset($GLOBALS['user']);

            // Spawn an error message; if we aren't called via AJAX just die, otherwise call dummy action
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

    /**
     * Controller initialization
     */
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

    /**
     * Dummy action; used as a replacement for the actual action when the before-method needs to cancel execution, but can't completely kill the script by dieing
     */
    public function action_noaction() {}

    /**
     * Hook for AJAX calls using JAPI
     * @return bool
     */
    public function action_japi() {
        // Get action, build method name by prepending "japi_"
        $action = $this->request->param('jaction');
        $method = "japi_{$action}";

        // Fail if we have no action
        if (!$action)
            return $this->error(\grge\E_HTTP_REQUEST_INCOMPLETE);

        // Call method, or fail if method does not exists
        if (method_exists($this, $method))
            return $this->$method();
        else return $this->error(\grge\E_HTTP_REQUEST_INVALID);
    }

    /**
     * Add some default data to the output chain, i.e. menus
     */
    private function render_defaults() {
        if (!isset($this->widgets['main-menu']) && static::$menu)
            $this->add_widget('main-menu', View::factory('menus/' . static::$menu)->render());
    }

    /**
     * Add a new content widget to the output chain
     * @param string $widget Widget name; if content parameter is missing, this will be used instead as context.
     * @param string|null $content Widget content. When missing, the first parameter is interpreted as widget content, using "content" as widget name
     */
    protected function add_widget($widget, $content = null) {
        if ($content === null) $this->widgets['content'] = $widget;
        else $this->widgets[$widget] = $content;
    }

    /**
     * Adds a notification to the output chain
     * @param string $type Notification type; if this is the only argument, it will be interpreted as message content instead
     * @param string|null $content Notification content
     * @param string|bool $title Notification title (optional)
     */
    protected function add_note($type, $content = null, $title = false) {
        if ($content === null) $this->notifications[] = array('type' => 'info', 'content' => $title, 'title' => false);
        else $this->notifications[] = array('type' => $type, 'content' => $content, 'title' => $title);
    }

    /**
     * Adds arbitrary data to the output chain
     * @param string|array $key Data key; if this is an array, each key/value-pair in that array will be added to the output chain. In that case, the other parameters are ignored.
     * @param mixed|null $data Data to add
     * @param bool $no_override If a key with the same name is already set, this controls weather the old key should be overwritten. The default is false (which means the old one will be overwritten)
     */
    protected function add_data($key, $data = null, $no_override = false) {
        if (is_array($key))
            $this->data = array_merge_recursive($this->data, $key);
        elseif (!isset($this->data[$key])) $this->data[$key] = $data;
        elseif (!$no_override) $this->data[$key] = array_merge_recursive($this->data[$key], $data);
    }

    /**
     * Renders the output chain as JSON data
     * @param null|bool|array $obj Set null to invoke render_defaults(); set to an array to invoke add_data(); set to anything else to just render the existing chain without adding any more data. If the script version indicates this is a beta environment, profiling data is always added regardless of this parameter
     * @return bool Always returns true
     * @throws Kohana_Exception
     */
    protected function render($obj = null) {
        $this->response->headers('Content-Type', 'application/json');

        // Invoke render_defaults() or add_data() depending on the object parameter
        if ($obj === null) {
            $this->render_defaults();
        } elseif (is_array($obj)) $this->add_data($obj);

        // Get version data, append profiling information when this is not a stable version
        $version_data = Kohana::$config->load('build.version');
        if ($version_data['stage'] < 3) {
            global $compression;

            $this->add_data('profiling', [
                'version' => "GRGE {$version_data['major']}.{$version_data['minor']}.{$version_data['service']}-{$version_data['stage']}-{$version_data['maintenance']}-{$version_data['build']} ({$version_data['date']})",
                'path' => $this->request->controller() . ' / ' . ($this->request->action() == 'japi' ? ($this->request->param('jaction') . ' (japi)') : $this->request->action()),
                'memory' => number_format((memory_get_peak_usage() - KOHANA_START_MEMORY) / 1024, 2).'KB',
                'time' => number_format(microtime(TRUE) - KOHANA_START_TIME, 5).'s',
                'compression' => number_format(($compression[0] == $compression[1]) ? 1 : $compression[1]/$compression[0] ,3)
            ], true);
        }

        // Add other content in out data chain
        $this->add_data('content', $this->widgets, true);
        $this->add_data('notifications', $this->notifications, true);

        // Render
        $this->response->body(json_encode($this->data, JSON_FORCE_OBJECT));
        return true;
    }

    /**
     * Causes an error to be rendered. This function writes to the output buffer, but does NOT halt script execution
     * @param mixed $c Error Code
     * @param mixed|null $additional_data Optional additional data to add to the output
     * @return bool Always returns false
     */
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