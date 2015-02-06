<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Admin_Gamepanel extends Controller_Admin_Admin {

    protected static $auto_require = ['CHEAT'];
    protected static $allow_skip_login = true;

    public function japi_unveil_map() {
        /** @global Model_Game $game */
        /** @global Model_Player $player */
        global $game, $player;

        if (!$game || !$player) return;

        $game->map()->uncover_all();

        $this->render();
    }

    public function japi_siege() {
        /** @global Model_Game $game */
        /** @global Model_Player $player */
        global $game, $player;

        if (!$game || !$player) return;

        $z = (int)$this->request->post('z');
        if ($z <> 0)
            $player->location()->zombie_factory()->accumulate_zombies($z);

        $this->render();
    }

    public function japi_regenerate() {
        /** @global Model_Game $game */
        /** @global Model_Player $player */
        global $game, $player;

        if (!$game || !$player) return;

        $player->stats_set(Model_Player::MP_STAT_ENERGY, 100, Model_Player::MP_STAT_HEALTH, 100, Model_Player::MP_STAT_HUNGER, 100, Model_Player::MP_STAT_THIRST, 100, Model_Player::MP_STAT_SLEEPY, 100);

        $this->render();
    }

    public function japi_spawn_items() {
        /** @global Model_Game $game */
        /** @global Model_Player $player */
        global $game, $player;

        if (!$game || !$player) return;

        $target_inv = $this->request->post('inventory');
        $sets = $this->request->post('data');

        if (!$sets) return;

        $instances = 0;

        foreach ($sets as $set) {
            $classname = 'Model_Items_' . str_replace('0::','Generic_',$set['id']);

            if (!class_exists($classname) ||
                !Tool_System::instance_of($classname, 'Model_Items_Abstract_Item') ||
                Tool_System::instance_of($classname, 'Model_Items_Abstract_Virtual')) {
                    $this->add_note('error',"{$set['id']} is not a valid item class!");
                    continue;
                }

            $reflector = new ReflectionClass($classname);

            if (!$reflector->isInstantiable()) {
                $this->add_note('error',"{$set['id']} is not an instantiable item class.");
                continue;
            }

            for ($i = 0; $i < min(100,$set['count']); $i++) {
                if (!isset($set['params'])) $set['params'] = [];
                try {
                    /** @var Model_Items_Abstract_Item $item */
                    $item = $reflector->newInstanceArgs($set['params']);
                } catch (Exception $e) {
                    $this->add_note('error',"Failed to instantiable {$set['id']}! " . $e->getMessage());
                    continue(2);
                }

                if ($target_inv == 'true') $player->inventory()->add($item);
                else $player->location()->inventory()->add($item);
                $instances++;

            }
        }

        $this->add_note($instances ? 'success' : 'error', $instances ? "$instances instances have been created." : 'No instances were created...');
        $this->render();
    }

    public function japi_list_items() {
        $path = APPPATH . 'classes/Model/Items';
        $list = [];

        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path));
        foreach($files as $name => $file) {
            $filename = $file->getFilename();
            if ($filename[0] == '.') continue;
            if (substr($filename, -4) !== '.php') continue;

            $filepath = str_replace('\\','/',$file->getPathName());
            $filepath = str_replace(str_replace('\\','/',$path),'',$filepath);

            /** @var Model_Items_Abstract_Item $classpath */
            $classpath = 'Model_Items' . substr(str_replace('/','_',$filepath), 0, -4);

            $reflection = new ReflectionClass($classpath);
            if (!$reflection->isInstantiable()) continue;

            if (!Tool_System::instance_of($classpath, 'Model_Items_Abstract_Item')) continue;
            if (Tool_System::instance_of($classpath, 'Model_Items_Abstract_Virtual')) continue;

            $tmp = [];
            $parameters = $reflection->getConstructor()->getParameters();

            $use_instances = ($classpath::getNumberOfTypes() > 0) && ($reflection->getConstructor()->getNumberOfRequiredParameters() == 0) && ($parameters[0]->getName() == 'type');

            for ($t = 0; $t < ($use_instances ? $classpath::getNumberOfTypes() : 1); $t++) {

                /** @var Model_Items_Abstract_Item|null $instance */
                $instance = $use_instances ? new $classpath($t) : null;
                $inum = 0;

                if ($use_instances)
                    $tmp = [[
                        'num' => 0,
                        'name' => 'type',
                        'optional' => true,
                        'default' => $t,
                        'force' => true,
                    ]];
                else foreach ($parameters as $parameter)
                    $tmp[] = [
                        'num' => $inum++,
                        'name' => $parameter->getName(),
                        'optional' => $parameter->isOptional(),
                        'default' => $parameter->isDefaultValueAvailable() ? $parameter->getDefaultValue() : null,
                        'force' => ($parameter->getName() == 'type' && $classpath::getNumberOfTypes() <= 1),
                    ];


                $list[] = [
                    'id' => str_replace(['Model_Items_Generic_','Model_Items_'],['0::',''],$classpath),
                    'name' => $use_instances ? __($instance->name()) : __($classpath::static_name()),
                    'desc' => $use_instances ? __($instance->description()) : __($classpath::static_description()),
                    'icon' => $use_instances ? $instance->icon() : $classpath::static_icon(),
                    'cat' => __(Model_Items_Abstract_Item::translateCatID($use_instances ? $instance->cat() : $classpath::static_cat())),
                    'params' => $tmp
                ];
            }

        }

        $this->render([
            'items' => $list
        ]);
    }
}