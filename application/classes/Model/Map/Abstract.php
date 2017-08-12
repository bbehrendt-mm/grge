<?php defined('SYSPATH') OR die('No direct access allowed.');

abstract class Model_Map_Abstract {

    const MMA_TYPE_OVERVIEW = 1;
    const MMA_TYPE_LABYRINTH = 2;
    
    const MMA_NUMBER_OF_TAGS = 16;

    protected static $map_type;
    protected static $map_grid_size = 2;

    protected $mapname = 'default';
    protected $mucfg = [];

    protected $paths = Array();
    protected $sublocation = null;

    protected $loc_notes = Array(
        /* 15 => Array('tag' => 0, 'text' => '') */
    );

    protected $loc_assoc = Array(
         /* 15 => Array('class' => 'Model_Places_Someplace', 'x' => 1, 'y' => 12, 'direction' => 12, 'visible' => false, 'dry' => 2) */
    );
    protected $id_assoc = Array(
        /* 'Model_Places_Someplace' => array(15,20) */
    );

    protected $assoc_cache = Array(
        /* 12 => Array('Model_Places_Someplace' => 3) */
    );

    protected $pos_cache = Array(
        'x' => array(),
        'y' => array(),
    );

    protected $fixed_id_assoc = Array();

    protected $sub_routing;

    /**
     * @var array [map id => routing node id]
     */
    protected $lib_routing = Array();

    /**
     * @var array [routing node id => map id]
     */
    protected $lib_routing_reverse = Array();

    protected $movement_cost_modifier = 1;

    public static function get_map_type() {
        return static::$map_type;
    }

    /**
     * @param $map
     * @param null $sub
     * @return Model_Map_Abstract
     */
    public static function factory($map, $sub = null) {
        $cls = static::meta($map,$sub)['engine'];
        /** @var Model_Map_Abstract $cls */
        return new $cls($map,$sub);
    }

    public function __construct($map, $sub) {
        $this->mapname = $map;
        $this->sublocation = $sub;
        $this->sub_routing = new Model_Routing(static::$map_grid_size);
    }

    public function get_location_notes($id) {
        return isset($this->loc_notes[$id]) ? $this->loc_notes[$id] : ['tag' => 0, 'text' => ''];
    }

    public function set_location_notes($id, $tag, $text) {
        if ($tag < 0 || $tag > Model_Map_Abstract::MMA_NUMBER_OF_TAGS) $tag = 0;
        if (isset($this->loc_assoc[$id])) {
            $this->loc_notes[$id] = ['tag' => $tag, 'text' => $tag > 0 ? $text : ''];
            return true;
        } else return false;

    }

    public function get_mapname() {
        return $this->mapname;
    }

    public function get_network() {
        return $this->sub_routing->get_network();
    }

    /**
     * Sets the movement cost modifier or returns the current modifier
     * @param null|number $set
     * @return int
     */
    public function movement_modifier($set = null) {
        $t = $this->movement_cost_modifier;
        if ($set !== null)
            $this->movement_cost_modifier = $set;
        return $t;
    }

    protected static function meta($map,$sub = null) {
        $cfg = (array)Kohana::$config->load("maps/{$map}.submeta");
        $type = $sub ? $sub : '.';
        return array_merge($cfg['..'],isset($cfg[$type]) ? $cfg[$type] : []);
    }

    /**
     * Returns the meta configuration object
     * @param String|null $type
     * @return array
     */
    protected function get_local_meta($type) {
        return static::meta($this->mapname,$type);
    }

    /**
     * Returns the configuration object
     * @param String|object|null $type
     * @return array
     */
    protected function get_config($type = null) {
        return (array)(($type === null) ? Kohana::$config->load("maps/{$this->mapname}.locations") : Tool_System::config_tree("maps/{$this->mapname}.locations", $type));
    }

    protected function &get_mutable_config($type) {
        $addr = is_object($type) ? get_class($type) : $type;
        if (!isset($this->mucfg[$addr])) $this->mucfg[$addr] = Tool_System::config_tree("maps/{$this->mapname}.locations", $type);
        return $this->mucfg[$addr];
    }

    public function has_location($id) {
        return isset($this->loc_assoc[$id]);
    }

    public function get_sublocation() {
        return $this->sublocation;
    }

    public function get_sublocation_description() {
        return $this->get_local_meta($this->sublocation)['name'];
    }

    /**
     * Adds a route
     * @param $from
     * @param $to
     */
    protected function push_route($from, $to) {
        if (!isset($this->paths[$from]))
            $this->paths[$from] = array();
        if (!in_array($to, $this->paths[$from]))
            $this->paths[$from][] = $to;
    }

    /**
     * Checks, if a new location ($class) can be connected to $target. If $target is not set, the function will just check if an instance of $class can be places in general.
     * @param string $class Class of location to place
     * @param null|int|array $target Target location
     * @return array|bool True, when a single target location was given and $class can be connected to that location; an array, when a target array was given, the array will contain all possible targets; false, when $class can not be connected to any locations in $target
     */
    protected function check_placement_limits($class, $target = null) {
        if (!($cfg = $this->get_config($class)))
            return false;

        if (($cfg['num'] <= 0 || $cfg['max_local'] <= 0) || (isset($this->id_assoc[$class]) && ($cfg['num'] <= count($this->id_assoc[$class]))))
            return false;

        if ($target === null)
            return true;

        $ret_as_array = is_array($target);
        if (!$ret_as_array)
            $target = Array($target);

        $target = array_filter($target,function($element) use ($class,$cfg) {
            return (!isset($this->assoc_cache[$element]) || !isset($this->assoc_cache[$element][$class]) || ($this->assoc_cache[$element][$class] < $cfg['max_local']));
        });

        $target_priority = array_filter($target,function($element) use ($class,$cfg) {
            return (!isset($this->assoc_cache[$element]) || array_sum($this->assoc_cache[$element]) < 3);
        });

        if ($target_priority)
            $target = $target_priority;

        if (count($target) == 0)
            return false;
        else return $ret_as_array ? array_values($target) : true;
    }

    /**
     * Updates the placement limits
     * @param string $class
     * @param int $target
     */
    protected function update_placement_limits($class, $target) {
        if (!$target) return;

        if (!isset($this->assoc_cache[$target]))
            $this->assoc_cache[$target] = Array($class => 1);
        elseif (!isset($this->assoc_cache[$target][$class]))
            $this->assoc_cache[$target][$class] = 1;
        else $this->assoc_cache[$target][$class]++;
    }

    /**
     * Updates position cache
     * @param number $x
     * @param number $y
     */
    protected function update_pos_cache($x, $y) {
        $this->pos_cache['x'][] = $x;
        $this->pos_cache['y'][] = $y;

        sort($this->pos_cache['x'], SORT_NUMERIC);
        sort($this->pos_cache['y'], SORT_NUMERIC);
    }

    /**
     * Adds a new location to location databases
     * @param int $location_id
     * @param string $location_class
     * @param int $x
     * @param int $y
     * @param $direction
     * @param boolean $visible
     * @param int $dry
     * @param bool $reserved
     * @param null|int $fixed_id
     */
    protected function catalog_location($location_id, $location_class, $x, $y, $direction, $visible, $dry = 0, $reserved = false, $fixed_id = null) {
        $this->loc_assoc[$location_id] = Array('class' => $location_class, 'x' => $x, 'y' => $y, 'direction' => $direction, 'visible' => $visible, 'dry' => $dry, 'reserved' => $reserved);
        if (!isset($this->id_assoc[$location_class]))
            $this->id_assoc[$location_class] = array($location_id);
        else $this->id_assoc[$location_class][] = $location_id;

        if ($fixed_id !== null)
            $this->fixed_id_assoc[$fixed_id] = $location_id;

        $this->update_pos_cache($x, $y);
    }

    /**
     * Adds a route
     * @param int $from Start point
     * @param int $to End point
     * @param bool $reverse Auto add the reversed path
     * @return bool True when the path was be added
     */
    public function add_route($from, $to, $reverse = true) {
        if (!isset($this->loc_assoc[$from]) || !isset($this->loc_assoc[$to]) )
            return false;

        $this->push_route($from, $to);
        if ($reverse) $this->push_route($to, $from);
        return true;
    }

    /**
     * Adds a location to the map. Position and connection paths are automatically set based on map configuration
     * @param Model_Places_Abstract_Place|string $location
     * @param boolean $visible
     * @param int $dry
     * @param null|int $fixed_id Fixed ID
     * @return bool
     * @throws Exception
     */
    abstract public function place_location($location, $visible, $dry = 0, $fixed_id = null);

    /**
     * @param Model_Places_Abstract_Place|string $location
     * @param $x
     * @param $y
     * @param $relative
     * @param $direction
     * @param $branchable
     * @param $root
     * @param $visible
     * @param $dry
     * @param $fixed_id
     * @return int
     * @throws Exception
     */
    public function implant_location($location,$x, $y, $relative, $direction, $branchable, $root, $visible, $dry, $fixed_id) {
        if ($relative) {
            $x += $this->loc_assoc[$root]['x'];
            $y += $this->loc_assoc[$root]['y'];
        }

        $is_reserved = is_string($location);
        if (is_string($location))
            $uin = Globals::CurrentGame()->uin()->reserve();
        elseif (!$location->uin()) $uin = Globals::CurrentGame()->uin()->set($location);
        else $uin = $location->uin();

        $this->catalog_location($uin, is_string($location) ? $location : get_class($location), $x, $y, $direction, $visible, $dry, $is_reserved, $fixed_id);
        $node = $this->sub_routing->add_node($x, $y, !$branchable);
        $this->lib_routing[$uin] = $node;
        if (!isset($this->lib_routing_reverse[$node])) $this->lib_routing_reverse[$node] = Array($uin);
        else $this->lib_routing_reverse[$node][] = $uin;

        if ($root !== null) {
            $this->add_route($uin, $root, true);
            $this->sub_routing->link_nodes($node, $this->lib_routing[$root]);
        }

        return $uin;
    }

    /**
     * Returns a location object by it's fixed ID
     * @param int $fixed_id
     * @return Model_Places_Abstract_Place|null
     */
    public function get_by_fixed_id($fixed_id) {
        if (isset($this->fixed_id_assoc[$fixed_id]))
            return Globals::CurrentGame()->uin()->get($this->fixed_id_assoc[$fixed_id], 'Model_Places_Abstract_Place');
        else return null;
    }

    public function add_location($class, $fixed_id = null) {
        if (!($data = $this->get_config($class)))
            return false;


        if ($this->place_location($class, true, $data['contortion'], $fixed_id)) {
            $this->sub_routing->compile();
            return true;
        } else return false;
    }

    /**
     * @param Model_Places_Abstract_Place $location
     * @return bool
     */
    public function insert_location($location) {
        $config = $this->get_config();
        $class = get_class($location);

        if (!isset($config[$class]))
            return false;

        $data = $config[$class];
        if (!$this->place_location($location, $data['obvious'], $data['contortion'], isset($data['fixed']) ? $data['fixed'] : null))
            return false;

        $this->sub_routing->compile();
        return true;
    }

    abstract public function auto_init();

    /**
     * Resolves a fixed ID to an actual ID
     * @param $fixed_id
     * @return null|int
     */
    public function resolve_fixed_id($fixed_id) {
        if (isset($this->fixed_id_assoc[$fixed_id]))
            return$this->fixed_id_assoc[$fixed_id];
        else return null;
    }

    public function uncover_all() {
        foreach ($this->loc_assoc as $lid => &$data) if (!$data['visible']) {
            $data['visible'] = true;
            if ($data['reserved']) {
                $class = $data['class'];
                $cls = new $class;
                Globals::CurrentGame()->uin()->fill_reservation($lid, $cls);
            }
        }
    }

    /**
     * Tries to unvail a new location from $id
     * @param int $id Current location
     * @param int $factor Chance modificator
     * @return bool|Model_Places_Abstract_Place|null false, when no location can be unvailed from here; null, when no location was unvailed, otherwise location object
     */
    public function attempt_unvail($id, $factor = 1) {
        if (!isset($this->loc_assoc[$id]))
            return false;
        if (!isset($this->paths[$id]))
            return null;

        $accum = 0;
        $g = array();
        foreach ($this->paths[$id] as $to)
            if (!$this->loc_assoc[$to]['visible']) {
                $dst_config = $this->get_config($this->loc_assoc[$to]['class']);
                $g[] = Array('chance' => $dst_config['chance'], 'value' => $to);
                $accum += $dst_config['chance'];
            }

        if ($accum == 0)
            return null;

        if ($this->loc_assoc[$id]['dry'] <= -1)
            $this->loc_assoc[$id]['dry'] = $accum * -($this->loc_assoc[$id]['dry']+1);
        elseif ($this->loc_assoc[$id]['dry'] < 0)
            $this->loc_assoc[$id]['dry'] = 0;

        $g[] = Array('chance' => round($this->loc_assoc[$id]['dry'] * $factor), 'value'=> null);

        if ($spawn = Tool_Gambling::roulette($g)) {
            $dst_config = $this->get_config($this->loc_assoc[$spawn]['class']);
            $this->loc_assoc[$spawn]['visible'] = true;
            if ($this->loc_assoc[$spawn]['reserved']) {
                $class = $this->loc_assoc[$spawn]['class'];
                Globals::CurrentGame()->uin()->fill_reservation($spawn, new $class);
            }

            $this->loc_assoc[$id]['dry'] += $dst_config['chance'];
            return Globals::CurrentGame()->location($spawn);
        } else return null;
    }

    protected function node_to_uin($node, $force = false) {
        if (!isset($this->lib_routing_reverse[$node]))
            return array();
        elseif ($force) return $this->lib_routing_reverse[$node];
        else {
            $tmp = array();
            foreach ($this->lib_routing_reverse[$node] as $id)
                if (isset($this->loc_assoc[$id]) && $this->loc_assoc[$id]['visible'])
                    $tmp[] = $id;
            return $tmp;
        }
    }

    /**
     * Returns a list of places you can go to from $id
     * @param int $id Start node ID
     * @param int $max_nodes Maximum number of nodes
     * @return array Format [destination => ['distance' => distance, 'tail' => array containing nodes to cross (without start and end node)],...]
     */
    public function build_route_array($id, $max_nodes = null) {
        $map = $this->sub_routing->build_route_array($this->lib_routing[$id]);

        $ret = array();
        foreach ($map as $data) {

            if ($max_nodes && count($data['tail']) > $max_nodes) continue;

            $final = array();
            $locals = false;
            foreach ($data['tail'] as $node) {
                $locals = $this->node_to_uin($node);
                foreach ($locals as $to)
                    $final[] = $to;
            }

            if (!$locals)
                continue;

            foreach ($locals as $local) {
                $tail = $final;
                foreach (array_keys($tail, $local) as $key)
                    unset($tail[$key]);
                $tail[] = $local;

                $ret[$local] = array(
                    'id' => $local,
                    'distance' => $data['distance'],
                    'nodes' => $data['tail'],
                    'tail' => $tail,
                );
            }
        }
        return $ret;
    }

    public function get_nodes() {
        return $this->sub_routing->get_nodes();
    }

    /**
     * Geturns thge position of a location, or null if $id does not point to a valid location
     * @param $id
     * @return array|null
     */
    public function get_position($id) {
        return isset($this->loc_assoc[$id]) ? array('x' => $this->loc_assoc[$id]['x'], 'y' => $this->loc_assoc[$id]['y']) : null;
    }

    /**
     * Returns the best route to go $from $to
     * @param int $from
     * @param int $to
     * @param null $max_nodes
     * @return array|null Null, when there is no such route
     */
    public function get_route($from, $to, $max_nodes = null) {
        $map = $this->build_route_array($from, $max_nodes);
        return (isset($map[$to])) ? $map[$to] : null;
    }

    public function get_distance($from, $to, $max_nodes = null) {
        $route = $this->get_route($from, $to, $max_nodes);
        return $route ? $route['distance'] : false;
    }

    /**
     * @return array
     */
    public function get_routes() {
        $ret = array();
        foreach ($this->paths as $from => $tos) foreach ($tos as $to) if ($this->loc_assoc[$from]['visible'] && $this->loc_assoc[$to]['visible']){
            $key = min($from, $to) . '_' . max($from, $to);
            if (!isset($ret[$key]))
                $ret[$key] = array('from' => min($from, $to), 'to' => max($from, $to), 'd1' => false, 'd2' => false);

            if ($from < $to)
                $ret[$key]['d1'] = true;
            else $ret[$key]['d2'] = true;
        }

        return array_values($ret);
    }

    /**
     * Gets the discovery rate for a specific location
     * @param $id
     * @param bool $full
     * @return float
     */
    public function get_discovery_rate($id, $full = false) {
        $found = $all = 0;

        if (!isset($this->paths[$id]))
            return 1;

        foreach ($this->paths[$id] as $to) {
            $all++;
            if ($this->loc_assoc[$to]['visible'])
                $found++;
        }

        if (!$full && $found > 0 && $all > 0) {
            $found--;
            $all--;
        }

        return ($all == 0) ? 1 : $found/$all;
    }

    public function get_adjacent_regions($id) {
        if (!isset($this->paths[$id]))
            return [];

        $ret = [];
        foreach ($this->paths[$id] as $to) {
            if ($this->loc_assoc[$to]['visible'])
                $ret[] = $to;
        }
        return $ret;
    }

    /**
     * Returns a list of location ids
     * @param null|string $type Restrict location type, or null for all locations
     * @param bool $show_hidden Set true to show locations that are hidden
     * @return int[]
     */
    public function get_locations($type = null, $show_hidden = false) {
        $ret = array();
        foreach ($this->loc_assoc as $id => $data)
            if (($data['visible'] || $show_hidden) && ($type === null || Tool_System::instance_of($data['class'], $type)))
                $ret[] = $id;
        return $ret;
    }

}