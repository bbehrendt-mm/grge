<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Test extends Controller {

    protected static $force_ajax = false;

    public function before(): void {
        if (Kohana::$environment !== Kohana::DEVELOPMENT) die('Sorry, Zombies infiltrated the lab.');

        //Load session, perform session checks
        $this->session = Session::instance();

        //Avoid caching!
        $this->response->headers('Cache-Control: no-cache, must-revalidate');
        $this->response->headers('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
    }

    public function action_rq(): void
    {
        self::dump('user_request', $this->session->get('request',[]));
    }

    public function action_labyrinth(): void
    {
        $id = $this->request->param('id');

        if ($id) {
            $id = explode('_',$id);
            $map = count($id) == 2 ? $id[0] : 'default';
            $sub = count($id) == 2 ? $id[1] : $id[0];
        } else {
            $map = count($id) == 2 ? $id[0] : 'default';
            $sub = count($id) == 2 ? $id[1] : 'hospital';
        }

        $m = new Model_Map_Labyrinth($map,$sub);
        $m->auto_init();

        echo '<table>';
        foreach ($m->scheme() as $x => $col) {
            echo '<tr>';

            foreach ($col as $y => $cell) {
                $location = $m->get_locations_at( $x, $y );

                $o = count($location) > 1 ? 'rgb(0,150,255)' : 'rgba(0,0,0,0)';
                $n = [];
                foreach ($location as $uin)
                    $n[] = htmlentities(Globals::CurrentGameF()->location( $uin )->name());
                $n = implode("; ", $n);

                switch ($cell) {
                    case Model_Map_Labyrinth::MML_WALL:
                        $r = 'rgb(0,0,0)';
                        break;
                    case Model_Map_Labyrinth::MML_CORRIDOR:
                        $r = 'rgb(150,150,150)';
                        break;
                    case Model_Map_Labyrinth::MML_INTERSECTION:
                        $r = 'rgb(150,200,150)';
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
                echo "<td title='$n' style='height: 16px; width: 16px; outline: 2px solid $o; background: $r;'>&nbsp;</td>";
            }
            echo '</tr>';
        }
        echo '</table>';
    }
}