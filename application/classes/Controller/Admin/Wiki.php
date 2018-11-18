<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Admin_Wiki extends Controller_Admin_Admin {

    protected static $auto_require = ['WIKI'];

    private function get_model_list($base, $filter_abstract = true): array
    {
        if (!$base) return [];

        $path = APPPATH . 'classes/Model/' . str_replace(['\\','/'], '_', $base);
        $list = [];

        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path));
        foreach($files as $name => $file) {
            $filename = $file->getFilename();
            if ($filename[0] === '.' || substr($filename, -4) !== '.php') continue;

            $filepath = str_replace(
                array('\\', str_replace('\\', '/', $path)), array('/', ''),
                $file->getPathName()
            );

            /** @var Model_Places_Abstract_Place $classpath */
            $classpath = "Model_$base" . substr(str_replace('/', '_', $filepath), 0, -4);

            $reflection = new ReflectionClass($classpath);
            if ($filter_abstract && !$reflection->isInstantiable()) continue;

            $list[] = $classpath;
        }

        return $list;
    }

    private function get_item_spawns($classpath) {
        $list = [];

        /** @var Model_Factory_Items $spawn */
        $spawn = Model_Factory_Items::read($classpath);

        if ($spawn) {
            $a = 0;
            $rem = $spawn->findings_left();

            $items = $spawn->get();
            arsort($items);

            /** @var string|Model_Items_Abstract_Item $item */
            foreach ($items as $item => $chance) {
                $a += $chance;
                $list[$item] = [
                    'name' => $item::static_name() ?: "[[$item]]",
                    'icon' => $item::static_icon(),
                    'chance' => round($chance * 100, 2),
                    'expect' => round($rem * $chance, ($rem * $chance > 1) ? 0 : 2),
                ];
            }

            if ($a) $list[''] = $rem;

            return $list;
        } else return null;
    }

    private function get_zombie_spawns($classpath) {
        $list = [];

        /** @var Model_Factory_Zombies $spawn */
        $spawn = Model_Factory_Zombies::read($classpath);

        if ($spawn) {
            $a = 0;

            $zombies = $spawn->get();
            arsort($zombies);
            /** @var string|Model_Combat_Zombies_Zombie $zomb */
            foreach ($zombies as $zomb => $chance) {
                $a += $chance;
                $list[$zomb] = [
                    'name' => ($tmp = (new $zomb())->name()) ? $tmp : "[[$zomb]]",
                    'icon' => 'zombie',
                    'chance' => round($chance * 100, 2),
                    'expect' => floor($spawn->get_strength(false)/$zomb::get_strength_quantifier()),
                ];
            }

            return $list;
        } else return null;
    }

    private function get_radar_data($classpath): array
    {
        /** @var Model_Factory_Zombies $spawn */
        $spawn = Model_Factory_Zombies::read($classpath);

        $rad = $spawn->get_radar_data();
        $rad[2] *= 100; $rad[3] *= 100;

        return [
            'strength' => $spawn->get_strength(false),
            'groups' => $spawn->get_max_group_count(),
            'c_attack' => $rad[2],
            'c_block' => $rad[3]
        ];
    }

    private function get_type($classpath): string {
        if (Tool_System::instance_of($classpath, Model_Places_Abstract_Node::cls()))
            return 'NODE';
        elseif (Tool_System::instance_of($classpath, Model_Places_Abstract_Hideout::cls()))
            return 'HIDEOUT';
        elseif (Tool_System::instance_of($classpath, Model_Places_Abstract_Trap::cls()))
            return 'TRAP';
        elseif (Tool_System::instance_of($classpath, Model_Places_Abstract_Xmas::cls()))
            return 'XMAS';
        else return '';
    }

    private function get_item_locations($locations, $item_findings): array
    {

        $ret = [];
        $items = $this->get_model_list('Items');

        /** @var string|Model_Items_Abstract_Item $item */
        foreach ($items as $item) if (!Tool_System::instance_of($item, Model_Items_Abstract_Virtual::cls())) {
            $tmp = [
                'name' => $item::static_name() ?: "[[$item]]",
                'icon' => $item::static_icon(),
                'locations' => [],
                'count' => 0,
            ];

            foreach ($locations as $location)
                $tmp['locations'][$location] = 0;

            $ret[$item] = $tmp;
        }

        foreach ($locations as $location)
            if (isset($item_findings[$location]))
                foreach ($item_findings[$location] as $item => $entry) if (isset($ret[$item])) {
                    $ret[$item]['locations'][$location] = $entry['expect'];
                    $ret[$item]['count'] += $entry['expect'];
                }

        foreach ($ret as &$listing)
            arsort($listing['locations']);
        unset($listing);
        uasort($ret, function($a, $b) {return $b['count'] - $a['count'];});

        return $ret;
    }

    public function action_main(): void
    {
        $this->add_widget(View::factory('admin/wiki/main')
            ->render());
        $this->render();
    }

    public function action_items(): void
    {
        $item_list = $this->get_model_list('Items', false);
        $items = [];

        foreach ($item_list as $item_class) {
            /** @var Model_Items_Abstract_Item|string $item_class */
            $reflection = new ReflectionClass($item_class);
            if (!$reflection->isInstantiable()) continue;

            $parameters = $reflection->getConstructor()->getParameters();
            /** @var Model_Items_Abstract_Item $instance */
            $instance =
                ($item_class::getNumberOfTypes() > 0) && ($reflection->getConstructor()->getNumberOfRequiredParameters() === 0) && ($parameters[0]->getName()
                    === 'type')
                ? new $item_class() : null;
            $singular = $item_class::getNumberOfTypes() === 1;

            $virtual = Tool_System::instance_of($item_class, Model_Items_Abstract_Virtual::cls());
            $trigger = Tool_System::instance_of($item_class, Model_Items_Virtual_Invoke_Abstract::cls());

            if ($virtual) $name = ($trigger ? '[T]' : '[V]') . ' ' . str_replace($trigger ? 'Model_Items_Virtual_Invoke_' : 'Model_Items_Virtual_','',$item_class);
            else $name = __(Tool_System::getItemInstanceName($item_class));
            if (!$name) $name = $item_class;


            if ($virtual) $icon = $trigger ? 'items/any2' : 'items/any';
            else $icon = Tool_System::getItemInstanceIcon($item_class);
            if (!$icon) $icon = 'items/any';

            $ancestors = [];
            $class = $reflection;

            $trn = [
                Model_Items_Abstract_Item::cls() => 'GRGE Item Base Class',
                Model_Combat_Weapon::cls() => 'Combat Weapon',
                Model_Items_Virtual_Invoke_Abstract::cls() => 'Wrapped Trigger'
            ];

            do {
                $cn = $class->getName();

                if (isset($trn[$cn])) $cn = $trn[$cn];
                else $cn = str_replace(['Model_Items_Abstract_','Model_Items_','Model_Combat_Weapons_'],['','','Weapon Class '], $cn);

                if ($class->isAbstract()) $cn = "[$cn]";

                $ancestors[] = $cn;
                if ($class->getName() === Model_Items_Abstract_Item::cls()) break;
            } while ($class = $class->getParentClass());

            $alias = [];
            if ($item_class::getNumberOfTypes() > 1)
                for ($t = 0; $t < $item_class::getNumberOfTypes(); $t++) {
                    $aname = Tool_System::getItemInstanceName($item_class, $t);
                    $aicon = Tool_System::getItemInstanceIcon($item_class, $t);

                    if (!$aname && !$aicon) continue;
                    $alias[] = [$aname ? __($aname) : $name, $aicon ?: $icon];
                }
            

            $code = [];
            foreach ($reflection->getMethods() as $method)
                if ($method->getDeclaringClass()->getName() === $item_class) {
                    $mth = [
                        'name' => $method->getName(),
                        'custom' => !$reflection->getParentClass() || !$reflection->getParentClass()->hasMethod($method->getName()),
                    ];
                    $code[] = $mth;
                }

            $static = [];
            $skp = ['static_info','instances_info'];
            foreach ($reflection->getStaticProperties() as $propertyName => $value)
                if ($value !== null && !in_array($propertyName, $skp, true)) {
                    ob_start();
                    var_dump($value);
                    $static[$propertyName] = ob_get_clean();
                }
                    

            $tmp = [
                'info' => [
                    'name' => $name,
                    'icon' => $icon,
                    'alias' => $alias,
                ],
                'lineage' => $ancestors,
                'code' => $code,
                'properties' => $static
            ];

            $items[$item_class] = $tmp;
        }

        uasort($items, function($a,$b) {
            return strcmp($a['info']['name'], $b['info']['name']);
        });

        $this->add_widget(View::factory('admin/wiki/items')
            ->set('items', $items)
            ->render());
        $this->render();
    }
    
    public function action_main_old(): void
    {
        $locations = $this->get_model_list('Places');

        $loc_names = [];
        $loc_items = [];
        $loc_zombies = [];
        $loc_radar = [];
        $loc_type = [];

        /** @var string|Model_Places_Abstract_Place $location */
        foreach ($locations as $location) {
            $loc_names[$location] = implode(' / ', $location::get_namelist());
            if ($tmp = $this->get_item_spawns($location)) $loc_items[$location] = $tmp;
            if ($tmp = $this->get_zombie_spawns($location)) $loc_zombies[$location] = $tmp;
            $loc_radar[$location] = $this->get_radar_data($location);
            $loc_type[$location] = $this->get_type($location);
        }

        $this->add_widget(View::factory('admin/wiki')
            ->set('index', $locations)
            ->set('names', $loc_names)
            ->set('items', $loc_items)
            ->set('zombies', $loc_zombies)
            ->set('radar', $loc_radar)
            ->set('types', $loc_type)
            ->set('atlas', $this->get_item_locations($locations, $loc_items))
            ->render());

        $this->render();
    }

}