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

    public function action_main(): void
    {
        $this->add_widget(View::factory('admin/wiki/main')
            ->render());
        $this->render();
    }

    public function action_locations(): void
    {
        $location_list = $this->get_model_list('Places', false);
        $locations = [];

        $groups = explode(',', $this->request->param('id', 'default'));
        list($item_group,$zed_group) = count($groups) > 1 ? $groups : [$groups[0],$groups[0]];

        foreach ($location_list as $location_class) {
            /** @var Model_Places_Abstract_Place|string $location_class */
            $reflection = new ReflectionClass($location_class);
            if (!$reflection->isInstantiable()) continue;

            $hideout = Tool_System::instance_of($location_class, Model_Places_Abstract_Hideout::cls());
            $node = Tool_System::instance_of($location_class, Model_Places_Abstract_Node::cls());

            $name_list = $location_class::get_namelist();
            $name_count = count($name_list);
            $name = __($name_list[0]);
            if ($hideout) $name = "[H] $name";
            else if ($node) $name = "[N] $name";

            $icon = $location_class::get_icon();

            $ancestors = [];
            $class = $reflection;

            $trn = [
                Model_Places_Abstract_Place::cls() => 'GRGE Location Base Class',
                Model_Places_Abstract_Node::cls() => 'Node',
                Model_Places_Abstract_Hideout::cls() => 'Hideout',
                Model_Places_Abstract_Trap::cls() => 'Trap',
                Model_Places_Abstract_Xmas::cls() => 'XMAS'
            ];

            do {
                $cn = $class->getName();

                if (isset($trn[$cn])) $cn = $trn[$cn];
                else $cn = str_replace(['Model_Places_Abstract_','Model_Places_'],'', $cn);

                if ($class->isAbstract()) $cn = "[$cn]";

                $ancestors[] = $cn;
                if ($class->getName() === Model_Places_Abstract_Place::cls()) break;
            } while ($class = $class->getParentClass());

            $alias = [];

            if ($name_count > 1)
                for ($t = 1; $t < $name_count; $t++) {
                    $aname =$name_list[$t];
                    if (!$aname) continue;
                    $alias[] = [__($aname) , $icon];
                }


            $code = [];
            foreach ($reflection->getMethods() as $method)
                if ($method->getDeclaringClass()->getName() === $location_class) {
                    $mth = [
                        'name' => $method->getName(),
                        'custom' => !$reflection->getParentClass() || !$reflection->getParentClass()->hasMethod($method->getName()),
                    ];
                    $code[] = $mth;
                }

            $static = [];
            $skp = ['location_name','description','icon','namelist'];
            foreach ($reflection->getStaticProperties() as $propertyName => $value)
                if ($value !== null && !in_array($propertyName, $skp, true)) {
                    ob_start();
                    var_dump($value);
                    $static[$propertyName] = ob_get_clean();
                }


            /** @var Model_Factory_Zombies $spawn */
            $spawn = Model_Factory_Zombies::read($location_class, $zed_group);
            $zombies = $spawn->get();

            $z_range = [];
            $z_avg = 0;
            foreach ($zombies as $z_class => $chance) {
                /** @var $z_class Model_Combat_Zombies_Zombie */
                $strength = (float)$z_class::get_strength_quantifier();

                $z_avg += $strength * $chance;
                $z_range = (empty($z_range))
                    ? [$strength,$strength]
                    : [min($strength,$z_range[0]),max($strength,$z_range[1])];
            }

            /** @var Model_Factory_Zombies $spawn */
            $items = Model_Factory_Items::read($location_class, $item_group);

            $item_chances = [];
            foreach ($items->get() as $item_class => $chance)
                $item_chances[] = [
                    'name' => Tool_System::instance_of($item_class, Model_Items_Abstract_Virtual::cls())
                        ? $item_class
                        : __(Tool_System::getItemInstanceName($item_class)),
                    'icon' => Tool_System::instance_of($item_class, Model_Items_Abstract_Virtual::cls())
                        ? 'items/any'
                        : Tool_System::getItemInstanceIcon($item_class),
                    'chance' => $chance
                ];

            uasort($item_chances, function($a,$b) {
                return $a['chance'] < $b['chance'];
            });

            $max_str =  $spawn->get_strength(false);

            $tmp = [
                'info' => [
                    'name' => $name,
                    'icon' => $icon,
                    'alias' => $alias,
                    'danger' => [
                        'str' => $max_str,
                        'chn' => $spawn->stat_chance(),
                        'blk' => $spawn->stat_blocking_factor(),
                        'ndg' => $spawn->get_strength(false) * $spawn->stat_chance(),
                        'num' => empty($z_range)
                            ? [0,0,0]
                            : [ floor($max_str / $z_range[1]), floor($max_str / $z_avg), floor($max_str / $z_range[0]) ],
                        'rte' => empty($z_range) ? 0 : round(12 * ($z_range[1] * $spawn->stat_chance() * $spawn->stat_blocking_factor()), 2)
                    ]
                ],
                'items' => $item_chances,
                'lineage' => $ancestors,
                'code' => $code,
                'properties' => $static
            ];

            $locations[$location_class] = $tmp;
        }

        uasort($locations, function($a,$b) {
            return strcmp($a['info']['name'], $b['info']['name']);
        });

        $this->add_widget(View::factory('admin/wiki/locations')
                              ->set('locations', $locations)
                              ->render());
        $this->render();
    }

    public function action_items(): void
    {
        $item_list = $this->get_model_list('Items', false);
        $items = [];

        $location_list = $this->get_model_list('Places', true);
        $loc_by_itemclass = [];

        $item_group = $this->request->param('id', 'default');

        foreach ($location_list as $location_class) {
            $name = __($location_class::get_namelist()[0]);
            $icon = $location_class::get_icon();

            $items_list = Model_Factory_Items::read($location_class, $item_group);
            if (empty($items_list)) continue;
            foreach ($items_list->get() as $item_class => $chance) {
                if (!isset($loc_by_itemclass[$item_class])) $loc_by_itemclass[$item_class] = [];

                $loc_by_itemclass[$item_class][] = [
                    'name' => $name, 'icon' => $icon, 'chance' => $chance
                ];
            }
        }

        foreach ( $loc_by_itemclass as &$list )
            uasort($list, function($a,$b) {
                return $a['chance'] < $b['chance'];
            });
        unset($list);

        foreach ($item_list as $item_class) {
            /** @var Model_Items_Abstract_Item|string $item_class */
            $reflection = new ReflectionClass($item_class);
            if (!$reflection->isInstantiable()) continue;

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
                'locations' => $loc_by_itemclass[$item_class] ?? [],
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
}