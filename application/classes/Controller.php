<?php defined('SYSPATH') or die('No direct script access.');

abstract class Controller extends Kohana_Controller {

    /**
     * @var Session $session
     */
    protected $session;
    protected static $force_ajax = true;
    private $widgets = array();

    protected function force_ajax() {
        if ($this->request->headers('X-Requested-With') != 'XMLHttpRequest' )
            die(Error::m(\grge\E_HTTP_AJAX_REQUIRED));
    }

    public function before() {
        //Init error class
        Error::i();

        //AJAX check
        if (static::$force_ajax)
            $this->force_ajax();

        //Avoid caching!
        $this->response->headers("Cache-Control: no-cache, must-revalidate");
        $this->response->headers("Expires: Sat, 26 Jul 1997 05:00:00 GMT");



        //TODO: Maintenance Mode

        $this->session = Session::instance();

        //TODO: Call security
    }

    public function action_japi() {
        $action = $this->request->param('jaction');
        $method = "japi_{$action}";

        if (!$action)
            return $this->error(\grge\E_HTTP_REQUEST_INCOMPLETE);

        if (method_exists($this, $method))
            return $this->$method();
        else return $this->error(\grge\E_HTTP_REQUEST_INVALID);
    }

    protected function add_widget($widget, $content = null) {
        if ($content === null) $this->widgets['content'] = $widget;
        else $this->widgets[$widget] = $content;
    }

    protected function render($obj = null) {
        $this->response->headers('Content-Type', 'application/json');

        $tmp = ($obj === null) ? array('content' => $this->widgets) : $obj;
        $this->response->body(json_encode($tmp, JSON_FORCE_OBJECT));
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
        return true;
    }
}