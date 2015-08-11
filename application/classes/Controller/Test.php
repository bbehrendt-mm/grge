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

    public function action_rq() {
        $this->dump('user_request', $this->session->get('request',[]));
    }

    public function action_ispawn() {
        $path = APPPATH . 'classes/Model/Places';
        $list = [];

        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path));
        foreach($files as $name => $file) {
            $filename = $file->getFilename();
            if ($filename[0] == '.') continue;
            if (substr($filename, -4) !== '.php') continue;

            $filepath = str_replace('\\', '/', $file->getPathName());
            $filepath = str_replace(str_replace('\\', '/', $path), '', $filepath);

            /** @var Model_Places_Abstract_Place $classpath */
            $classpath = 'Model_Places' . substr(str_replace('/', '_', $filepath), 0, -4);

            $reflection = new ReflectionClass($classpath);
            if (!$reflection->isInstantiable()) continue;

            /** @var Model_Places_Abstract_Place $classpath */
            if (!Tool_System::instance_of($classpath, 'Model_Places_Abstract_Place')) continue;

            echo "<h2>" . implode(' / ',$classpath::get_namelist()) . "</h2>";
            $spawn = Model_Itemfactory::read($classpath);

            echo "<table cellpadding='4px'>";
            if ($spawn) {
                $a = 0;
                $rem = $spawn->findings_left();

                $list = $spawn->get();
                arsort($list);
                /** @var Model_Items_Abstract_Item $item */
                foreach ($list as $item => $chance) {
                    $a += $chance;
                    $dchance = round($chance * 100, 2);
                    $item = $item::static_name() ? $item::static_name() : "[[$item]]";
                    echo "<tr><td>$item</td><td>$dchance %</td><td>" . round($rem * $chance, ($rem * $chance > 1) ? 0 : 2) . "</td></tr>";
                }
                if (!$a)
                    echo "<tr><td><b>Empty!</b></td><td></td></tr>";
                else {
                    $a = round($a * 100, 10);
                    echo "<tr><td><b>--- SUM ---</b></td><td>$a %</td><td></td></tr>";
                    echo "<tr><td><b>Erw. Funde</b></td><td>$rem</td><td></td></tr>";
                }
            }
            else echo "<b>No data!</b>";
            echo "</table>";
        }
    }

    public function action_combat() {

        $battle = Model_Combat_Field::factory()
            ->add_combatant(1, [
                Model_Combat_Actor::factory()
                    ->name('Brainbox', Model_Combat_Actor::MCA_TYPE_PLAYER)
                    ->add_weapon(new Model_Test_Machete())
                    ->strength(84, 100, 1)
                    ->stats(8,12,1,0),
                Model_Combat_Actor::factory()
                    ->name('Dog ("Veemon")', Model_Combat_Actor::MCA_TYPE_PET)
                    ->add_weapon(new Model_Test_Machete())
                    ->strength(19, 20, 1)
                    ->stats(10,0,0,0),
            ])
            ->add_combatant(2, [
                Model_Combat_Actor::factory()
                    ->name('Walker', Model_Combat_Actor::MCA_TYPE_ZOMBIE)
                    ->add_weapon(new Model_Test_Claw())
                    ->strength(3,3,4)
                    ->stats(5,2,0,0),
                Model_Combat_Actor::factory()
                    ->name('Shambler', Model_Combat_Actor::MCA_TYPE_ZOMBIE)
                    ->add_weapon(new Model_Test_Claw())
                    ->strength(1,1,3)
                    ->stats(2,0,0,0),
                Model_Combat_Actor::factory()
                    ->name('Shambler', Model_Combat_Actor::MCA_TYPE_ZOMBIE)
                    ->add_weapon(new Model_Test_Claw())
                    ->strength(1,1,5)
                    ->stats(2,0,0,0),
                Model_Combat_Actor::factory()
                    ->name('Zombie Dog', Model_Combat_Actor::MCA_TYPE_PET)
                    ->add_weapon(new Model_Test_Claw())
                    ->strength(15,15,1)
                    ->stats(6,2,2,0),
            ])
            ->init_positions(24, 5)
            ->begin();

        $log = $battle->get_scene();

        echo "<h2>ZombVival Combat System V3 (GRGE_2.1 / Season 8)</h2>";
        echo "<b>Battle text log below:</b><br />";
        echo "<pre>$log</pre>";
        echo "Log end.";
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