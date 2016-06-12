<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Admin_Gamepanel extends Controller_Admin_Admin {

    protected static $auto_require = ['CHEAT'];
    protected static $allow_skip_login = true;

    public function japi_unveil_map() {
        /** @global Model_Game $game */
        /** @global Model_Player $player */
        global $game, $player;

        if (!$game || !$player) return;

        $game->map($player->location_class())->uncover_all();

        $this->render();
    }

    public function japi_force_battle() {
        /** @global Model_Player $player */
        global $player;

        $zombies = $player->location()->zombie_factory()->spawn(true);
        if ($zombies) Tool_Scripts::combat([Tool_Scripts::at_location($player->location_class()), $zombies], true, 20, $player->location());
    }

    public function japi_purge_log() {
        /** @global Model_Player $player */
        global $player;

        $player->log()->clear();
        $player->location()->log()->clear();

        $this->render();
    }

    public function japi_siege() {
        /** @global Model_Game $game */
        /** @global Model_Player $player */
        global $game, $player;

        if (!$game || !$player) return;

        $z = (int)$this->post('z');
        if ($z >= 0)
            $player->location()->zombie_factory()->accumulation($z);

        $this->render();
    }

    public function japi_regenerate() {
        /** @global Model_Game $game */
        /** @global Model_Player $player */
        global $game, $player;

        if (!$game || !$player) return;

        $player->get_status()->set(Model_Status::MS_STAT_ENERGY, 100, Model_Status::MS_STAT_HEALTH, 100, Model_Status::MS_STAT_HUNGER, 100, Model_Status::MS_STAT_THIRST, 100, Model_Status::MS_STAT_SLEEPY, 100);

        $this->render();
    }

    public function japi_spawn_items() {
        /** @global Model_Game $game */
        /** @global Model_Player $player */
        global $game, $player;

        if (!$game || !$player) return;

        $target_inv = $this->post('inventory');
        $sets = $this->post('data');

        if (!$sets) return;

        $instances = 0;

        foreach ($sets as $set) {
            $classname = 'Model_Items_' . str_replace(['0::','v::','t::'],['Generic_','Virtual_','Virtual_Invoke_'],$set['id']);

            if (!class_exists($classname) ||
                !Tool_System::instance_of($classname, 'Model_Items_Abstract_Item')) {
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
                foreach ($set['params'] as &$v)
                    if ($v === 'null') $v = null;

                try {
                    /** @var Model_Items_Abstract_Item $item */
                    $item = $reflector->newInstanceArgs($set['params']);
                } catch (Exception $e) {
                    $this->add_note('error',"Failed to instantiate {$set['id']}! " . $e->getMessage());
                    continue(2);
                }

                if (Tool_System::instance_of($item, Model_Items_Virtual_Invoke_Abstract::cls())) {
                    /** @var $item Model_Items_Virtual_Invoke_Abstract */
                    $item->trigger_spawn($player->location(), $player);
                    $item->grind();
                    $item = null;
                } else {
                    if ($target_inv == 'true') $player->inventory()->add($item);
                    else $player->location()->inventory()->add($item);

                }

                $instances++;
            }
        }

        $this->add_note($instances ? 'success' : 'error', $instances ? "$instances instances have been created." : 'No instances were created...');
        $this->render();
    }

    public function japi_skip() {
        /** @global Model_Game $game */
        /** @global Model_Player $player */
        global $game, $player;

        if (!$game || !$player) return;

        $ticks = (int)$this->post('ticks');
        if ($ticks > 0)
            $game->fast_forward($ticks);

        $this->render();
    }

    public function japi_custom_battle() {
        /** @global Model_Player $player */
        global $player;

        $config = $this->post('data');
        $zombies = [];

        foreach ($config as $entry) {
            /** @var Model_Combat_Zombies_Zombie $classpath */
            $classpath = "Model_Combat_Zombies_{$entry['type']}";
            if (!Tool_System::instance_of($classpath, 'Model_Combat_Zombies_Zombie') || (int)$entry['count'] <= 0 || (int)$entry['distance'] < 0)
                continue;

            $zombies[] = $classpath::factory()->count((int)$entry['count'])->set_distance((int)$entry['distance']);
        }

        if ($zombies) Tool_Scripts::combat([Tool_Scripts::at_location($player->location_class()), $zombies], true, 20, $player->location());
        $this->render();
    }

    public function japi_list_zombies() {
        $path = APPPATH . 'classes/Model/Combat/Zombies';
        $list = [];

        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path));
        foreach($files as $name => $file) {
            $filename = $file->getFilename();
            if ($filename[0] == '.') continue;
            if (substr($filename, -4) !== '.php') continue;

            $filepath = str_replace('\\','/',$file->getPathName());
            $filepath = str_replace(str_replace('\\','/',$path),'',$filepath);

            /** @var Model_Combat_Zombies_Zombie $classpath */

            $classpath = 'Model_Combat_Zombies' . substr(str_replace('/','_',$filepath), 0, -4);

            $reflection = new ReflectionClass($classpath);
            if (!$reflection->isInstantiable() || Tool_System::instance_of($classpath, 'Model_Combat_Zombies_Ghul')) continue;

            $list[] = [
                'id' => str_replace('Model_Combat_Zombies_','',$classpath),
                'name' => __($classpath::factory()->name()),
            ];
        }

        $this->render([
            'zombies' => $list
        ]);
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
            $virtual = Tool_System::instance_of($classpath, 'Model_Items_Abstract_Virtual');
            $trigger = Tool_System::instance_of($classpath, Model_Items_Virtual_Invoke_Abstract::cls());

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
                    'id' => str_replace(['Model_Items_Virtual_Invoke_','Model_Items_Virtual_','Model_Items_Generic_','Model_Items_'],['t::','v::','0::',''],$classpath),
                    'name' => $virtual ? ($trigger ? '[ITW]' : '[VCI]') : ($use_instances ? __($instance->name()) : __($classpath::static_name())),
                    'desc' => $virtual ? '' : ($use_instances ? __($instance->description()) : __($classpath::static_description())),
                    'icon' => $virtual ? ($trigger ? 'items/any2' : 'items/any') : ($use_instances ? $instance->icon() : $classpath::static_icon()),
                    'cat' => $virtual ? ($trigger ? 'Trigger Wrappers' : 'Virtual Control Items') : __(Model_Items_Abstract_Item::translateCatID($use_instances ? $instance->cat() : $classpath::static_cat())),
                    'params' => $tmp
                ];
            }

        }

        $this->render([
            'items' => $list
        ]);
    }
}