<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Map_Circular extends Model_Map_Abstract {

    protected static $map_type = Model_Map_Abstract::MMA_TYPE_OVERVIEW;

    /**
     * Produces a position within $distance from $root
     *
     * @param int|array             $distance Distance; can be a single int value to use as fixed distance, or an array with 2 elements containing boundaries [min,max]
     * @param string                $spawn    Spawn class
     * @param null|int|string|array $root     Root position; When omitted, 0/0 is used as position; when given as int, $root is treated as location id; when given as String, $root is interpreted as location classname, if more locations with this classname exist, one will be randomly selected; when given as array, the function will select one of the elements (that can be used to produce a location) randomly or return false when it can't find one
     *
     * @return array|bool false, if no position could be determined; otherwise an array in the format ['x' => x, 'y' => y, 'root' => root location id|null]
     * @throws Kohana_Exception
     */
    protected function get_random_location($distance, $spawn, $root = null) {

        if (is_array($root)) {
            shuffle($root);
            foreach ($root as $elem)
                if ($tmp = $this->get_random_location($distance, $spawn, $elem))
                    return $tmp;
            return false;
        }

        $root_location_id = null;
        if ($root === null) {
            $data = array('type' => null, 'x' => 0, 'y' => 0, 'direction' => null);
        } elseif (is_int($root) && isset($this->loc_assoc[$root]))
            $data = $this->loc_assoc[$root];
        elseif ((is_int($root) && !isset($this->loc_assoc[$root])) || (!is_int($root) && !isset($this->id_assoc[$root])))
            return false;
        else {
            if (!($list = $this->check_placement_limits($spawn, Tool_System::config_tree($this->id_assoc, $root))))
                return false;
            $root_location_id = $list[random_int(0, count($list) - 1)];
            $data = $this->loc_assoc[$root_location_id];
        }

        $limit = ($distance <= 10) ? 90 : 45;
        $grad = ($data['direction'] === null) ? random_int(0,359) : random_int($data['direction'] - $limit, $data['direction'] + $limit);
        while ($grad > 359) $grad -= 360;
        while ($grad < 0) $grad += 360;

        $rad = ($grad * M_PI / 180);
        if (is_array($distance))
            $distance = random_int($distance[0], $distance[1]);

        return array('x' => $distance * cos($rad) + $data['x'], 'y' => $distance * sin($rad) + $data['y'], 'root' => $root_location_id, 'direction' => ($root_location_id === null) ? null : $grad);
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
    public function place_location($location, $visible, $dry = 0, $fixed_id = null): bool {
        if (is_string($location) && $visible)
            $location = new $location;

        if (!(Tool_System::instance_of($location, 'Model_Places_Abstract_Place'))
            || !($cfg = &$this->get_mutable_config($location))
            || !($pos = $this->get_random_location($cfg['distance'], is_string($location) ? $location : get_class($location), empty($cfg['force_root']) ? $cfg['root'] : array_pop($cfg['force_root'])))) return false;

        $this->implant_location($location, $pos['x'], $pos['y'], false, $pos['direction'],$cfg['branchable'],$pos['root'],$visible,$dry,$fixed_id);

        $this->update_placement_limits(is_string($location) ? $location : get_class($location), $pos['root']);

        return true;
    }

    public function auto_init(): void {
        $config = $this->get_config();

        $smart_routing = true;

        //Place locations
        $keep_going = true;
        $iteration = 0;
        while ($keep_going) {
            $keep_going = false;
            foreach ($config as $class => $data) if ($data['auto'] && ($data['sub'] === $this->sublocation || (is_array($data['sub']) && in_array($this->sublocation, $data['sub'])))) {

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
}