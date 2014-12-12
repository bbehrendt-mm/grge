<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Map {

    private $mapname = 'default';

    private $paths = Array();
    private $sublocation = null;

    private $loc_assoc = Array(
         /* 15 => Array('class' => 'Model_Places_Someplace', 'x' => 1, 'y' => 12, 'direction' => 12, 'visible' => false, 'dry' => 2) */
    );
    private $id_assoc = Array(
        /* 'Model_Places_Someplace' => array(15,20) */
    );

    private $assoc_cache = Array(
        /* 12 => Array('Model_Places_Someplace' => 3) */
    );

    private $pos_cache = Array(
        'x' => array(),
        'y' => array(),
    );

    private $fixed_id_assoc = Array();

    private $sub_routing;

    /**
     * @var array [map id => routing node id]
     */
    private $lib_routing = Array();

    /**
     * @var array [routing node id => map id]
     */
    private $lib_routing_reverse = Array();

    private $movement_cost_modifier = 1;

    public function __construct($map) {
        $this->mapname = $map;
        $this->sub_routing = new Model_Routing();
    }

    public function get_mapname() {
        return $this->mapname;
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

    /**
     * Returns the configuration object
     * @param String|object|null $type
     * @return array
     */
    private function get_config($type = null) {
        return (array)(($type === null) ? Kohana::$config->load("maps/{$this->mapname}") : Tool_System::config_tree("maps/{$this->mapname}", $type));
    }

    public function has_location($id) {
        return isset($this->loc_assoc[$id]);
    }

    public function get_sublocation() {
        return $this->sublocation;
    }

    /**
     * Adds a route
     * @param $from
     * @param $to
     */
    private function push_route($from, $to) {
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
    private function check_placement_limits($class, $target = null) {
        if (!($cfg = $this->get_config($class)))
            return false;

        if (($cfg['num'] <= 0 || $cfg['max_local'] <= 0) || (isset($this->id_assoc[$class]) && ($cfg['num'] <= count($this->id_assoc[$class]))))
            return false;

        if ($target === null)
            return true;

        $ret_as_array = is_array($target);
        if (!$ret_as_array)
            $target = Array($target);

        $ret = array();

        foreach ($target as $element)
            if (!isset($this->assoc_cache[$element]) || !isset($this->assoc_cache[$element][$class]) || ($this->assoc_cache[$element][$class] < $cfg['max_local']))
                $ret[] = $element;

        if (count($ret) == 0)
            return false;
        else return $ret_as_array ? $ret : true;
    }

    /**
     * Updates the placement limits
     * @param string $class
     * @param int $target
     */
    private function update_placement_limits($class, $target) {
        if (!$target) return;

        if (!isset($this->assoc_cache[$target]))
            $this->assoc_cache[$target] = Array($class => 1);
        elseif (!isset($this->assoc_cache[$target][$class]))
            $this->assoc_cache[$target][$class] = 1;
        else $this->assoc_cache[$target][$class]++;
    }

    /**
     * Produces a position within $distance from $root
     * @param int|array $distance Distance; can be a single int value to use as fixed distance, or an array with 2 elements containing boundaries [min,max]
     * @param string $spawn Spawn class
     * @param null|int|string|array $root Root position; When omitted, 0/0 is used as position; when given as int, $root is treated as location id; when given as String, $root is interpreted as location classname, if more locations with this classname exist, one will be randomly selected; when given as array, the function will select one of the elements (that can be used to produce a location) randomly or return false when it can't find one
     * @internal param string $spawncfg Spawn object class
     * @return array|bool false, if no position could be determined; otherwise an array in the format ['x' => x, 'y' => y, 'root' => root location id|null]
     */
    private function get_random_location($distance, $spawn, $root = null) {

        if (is_array($root)) {
            shuffle($root);
            foreach ($root as $elem)
                if ($tmp = $this->get_random_location($distance, $spawn, $elem))
                    return $tmp;
            return false;
        }

        if ($root === null) {
            $data = array('type' => null, 'x' => 0, 'y' => 0, 'direction' => null);
            $root_location_id = null;
        } elseif (is_int($root) && isset($this->loc_assoc[$root]))
            $data = $this->loc_assoc[$root];
        elseif ((is_int($root) && !isset($this->loc_assoc[$root])) || (!is_int($root) && !isset($this->id_assoc[$root])))
            return false;
        else {
            if (!($list = $this->check_placement_limits($spawn, Tool_System::config_tree($this->id_assoc, $root))))
                return false;
            $root_location_id = $list[mt_rand(0, count($list) - 1)];
            $data = $this->loc_assoc[$root_location_id];
        }

        $limit = ($distance <= 10) ? 90 : 45;
        $grad = ($data['direction'] === null) ? mt_rand(0,359) : mt_rand($data['direction'] - $limit, $data['direction'] + $limit);
        while ($grad > 359) $grad -= 360;
        while ($grad < 0) $grad += 360;

        $rad = ($grad * M_PI / 180);
        if (is_array($distance))
            $distance = mt_rand($distance[0], $distance[1]);

        return array('x' => $distance * cos($rad) + $data['x'], 'y' => $distance * sin($rad) + $data['y'], 'root' => $root_location_id, 'direction' => ($root_location_id === null) ? null : $grad);
    }

    /**
     * Updates position cache
     * @param number $x
     * @param number $y
     */
    private function update_pos_cache($x, $y) {
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
    private function catalog_location($location_id, $location_class, $x, $y, $direction, $visible, $dry = 0, $reserved = false, $fixed_id = null) {
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
     */
    public function place_location($location, $visible, $dry = 0, $fixed_id = null) {
        /**
         * @global $game Model_Game
         */
        global $game;

        if (is_string($location) && $visible)
            $location = new $location;

        if (!(Tool_System::instance_of($location, 'Model_Places_Abstract_Place'))
            || !($cfg = $this->get_config($location))
            || !($pos = $this->get_random_location($cfg['distance'], is_string($location) ? $location : get_class($location), $cfg['root']))) return false;

        $is_reserved = is_string($location);
        if (is_string($location))
            $uin = $game->uin()->reserve();
        elseif (!$location->uin()) $uin = $game->uin()->set($location);
        else $uin = $location->uin();

        $this->catalog_location($uin, is_string($location) ? $location : get_class($location), $pos['x'], $pos['y'], $pos['direction'], $visible, $dry, $is_reserved, $fixed_id);
        $node = $this->sub_routing->add_node($pos['x'], $pos['y'], !$cfg['branchable']);
        $this->lib_routing[$uin] = $node;
        if (!isset($this->lib_routing_reverse[$node])) $this->lib_routing_reverse[$node] = Array($uin);
        else $this->lib_routing_reverse[$node][] = $uin;

        if ($pos['root'] !== null) {
            $this->add_route($uin, $pos['root'], true);
            $this->sub_routing->link_nodes($node, $this->lib_routing[$pos['root']]);
        }

        $this->update_placement_limits(is_string($location) ? $location : get_class($location), $pos['root']);

        return true;
    }

    /**
     * Returns a location object by it's fixed ID
     * @param int $fixed_id
     * @return Model_Places_Abstract_Place|null
     */
    public function get_by_fixed_id($fixed_id) {
        /**
         * @global $game Model_Game
         */
        global $game;

        if (isset($this->fixed_id_assoc[$fixed_id]))
            return $game->uin()->get($this->fixed_id_assoc[$fixed_id], 'Model_Places_Abstract_Place');
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

    public function auto_init($submapid = null) {
        $config = $this->get_config();
        $this->sublocation = $submapid;

        $smart_routing = true;

        //Place locations
        $keep_going = true;
        $iteration = 0;
        while ($keep_going) {
            $keep_going = false;
            foreach ($config as $class => $data) if ($data['auto'] && ($data['sub'] === $submapid || (is_array($data['sub']) && in_array($submapid, $data['sub'])))) {

                if ($data['iteration'] > $iteration) {
                    $keep_going = true;
                    continue;
                }

                if (isset($data['nosmartrouting']) && $data['nosmartrouting'])
                    $smart_routing = false;

                if ($this->place_location($class, $data['obvious'], $data['contortion'], isset($data['fixed']) ? $data['fixed'] : null))
                    /** @noinspection PhpUnusedLocalVariableInspection */
                    $keep_going = true;

                if (!$this->check_placement_limits($class))
                    unset($config[$class]);
            }
            $iteration++;

        }

        if ($smart_routing) $this->sub_routing->compile();
    }

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
        global $game;
        foreach ($this->loc_assoc as $lid => &$data) if (!$data['visible']) {
            $data['visible'] = true;
            if ($data['reserved']) {
                $class = $data['class'];
                $game->uin()->fill_reservation($lid, new $class);
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
        /**
         * @global $game Model_Game
         */
        global $game;

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
                $game->uin()->fill_reservation($spawn, new $class);
            }

            $this->loc_assoc[$id]['dry'] += $dst_config['chance'];
            return $game->location($spawn);
        } else return null;
    }

    private function node_to_uin($node, $force = false) {
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
     * @return array Format [destination => ['distance' => distance, 'tail' => array containing nodes to cross (without start and end node)],...]
     */
    public function build_route_array($id) {
        $map = $this->sub_routing->build_route_array($this->lib_routing[$id]);

        $ret = array();
        foreach ($map as $data) {

            $final = array();
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
     * @return null|array Null, when there is no such route
     */
    public function get_route($from, $to) {
        $map = $this->build_route_array($from);
        return (isset($map[$to])) ? $map[$to] : null;
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