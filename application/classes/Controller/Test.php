<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Test extends Controller {

    protected static $force_ajax = false;

    public function before() {
        //if (Kohana::$environment !== Kohana::DEVELOPMENT) die('Sorry, Zombies infiltrated the lab.');

        //Load session, perform session checks
        $this->session = Session::instance();

        //Avoid caching!
        $this->response->headers("Cache-Control: no-cache, must-revalidate");
        $this->response->headers("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
    }

    public function action_rq() {
        $this->dump($this->session->get('request',[]));
    }

    public function action_labyrinth() {
        $m = new Model_Map_Labyrinth('default','hospital');
        $m->auto_init();

        echo "<table>";
        foreach ($m->scheme() as $col) {
            echo "<tr>";
            foreach ($col as $cell) {
                switch ($cell) {
                    case Model_Map_Labyrinth::MML_WALL:
                        $r = 'rgb(0,0,0)';
                        break;
                    case Model_Map_Labyrinth::MML_CORRIDOR:
                        $r = 'rgb(150,150,150)';
                        break;
                    case Model_Map_Labyrinth::MML_INTERSECTION:
                        $r = 'rgb(50,255,50)';
                        break;
                    case Model_Map_Labyrinth::MML_FAR:
                        $r = 'rgb(200,150,150)';
                        break;
                    case Model_Map_Labyrinth::MML_ENTRYPOINT:
                        $r = 'rgb(220,220,220)';
                        break;
                    default:
                        $r = 'rgb(255,0,0)';
                }
                echo "<td style='height: 16px; width: 16px; background: $r;'>&nbsp;</td>";
            }
            echo "</tr>";
        }
        echo "</table>";
    }
}