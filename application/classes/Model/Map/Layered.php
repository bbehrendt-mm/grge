<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Map_Layered extends Model_Map_Circular {

    protected static $map_grid_size = 1;

    /**
     * Produces a position within $distance from $root
     *
     * @param int|array             $distance Distance; can be a single int value to use as fixed distance, or an array with 2 elements containing boundaries [min,max]
     * @param                       $to
     * @param string                $spawn    Spawn class
     * @param null|int|string|array $root     Root position; When omitted, 0/0 is used as position; when given as int, $root is treated as location id; when given as String, $root is interpreted as location classname, if more locations with this classname exist, one will be randomly selected; when given as array, the function will select one of the elements (that can be used to produce a location) randomly or return false when it can't find one
     *
     * @return array|bool false, if no position could be determined; otherwise an array in the format ['x' => x, 'y' => y, 'root' => root location id|null]
     * @throws Kohana_Exception
     */
    protected function get_random_location_layered($distance, $to, $spawn, $root = null) {

        if (is_array($root)) {
            shuffle($root);
            foreach ($root as $elem)
                if ($tmp = $this->get_random_location_layered($distance, $spawn, $elem))
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

        $distance = random_int($distance[0], $distance[1]);
        $factor = ['x' => 0, 'y' => 0];
        switch ($to) {
            case 'top': case 'bottom': case 'shaft': $factor['y'] = ($to == 'top' || ($to != 'bottom' && random_int(0,1))) ? -1 : 1; break;
            case 'left': case 'right': case 'level': $factor['x'] = ($to == 'right' || ($to != 'left' && random_int(0,1))) ? 1 : -1; break;
            case 'same': default: $factor = $data['direction']; break;
        }

        return array('x' => $distance * $factor['x'] + $data['x'], 'y' => $distance * $factor['y'] + $data['y'], 'root' => $root_location_id, 'direction' => ($root_location_id === null) ? null : $factor);
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
    public function place_location($location, $visible, $dry = 0, $fixed_id = null) {
        if (is_string($location) && $visible)
            $location = new $location;

        if (!(Tool_System::instance_of($location, 'Model_Places_Abstract_Place'))
            || !($cfg = &$this->get_mutable_config($location))
            || !($pos = $this->get_random_location_layered($cfg['distance'], $cfg['to'], is_string($location) ? $location : get_class($location), empty($cfg['force_root']) ? $cfg['root'] : array_pop($cfg['force_root'])))) return false;

        $this->implant_location($location, $pos['x'], $pos['y'], false, $pos['direction'],$cfg['branchable'],$pos['root'],$visible,$dry,$fixed_id);

        $this->update_placement_limits(is_string($location) ? $location : get_class($location), $pos['root']);

        return true;
    }
}