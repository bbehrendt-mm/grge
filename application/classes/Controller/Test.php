<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Test extends Controller {

    protected static $force_ajax = false;

    public function before() {
        if (Kohana::$environment !== Kohana::DEVELOPMENT) die('Sorry, Zombies infiltrated the lab.');

        //Load session, perform session checks
        $this->session = Session::instance();

        //Avoid caching!
        $this->response->headers("Cache-Control: no-cache, must-revalidate");
        $this->response->headers("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
    }

    private function dump($v) {
        echo "<pre />"; var_dump($v); echo "</pre>";
    }

    public function action_rq() {
        $this->dump($this->session->get('request',[]));
    }
}