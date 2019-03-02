<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Map_Labyrinth extends Model_Map_Abstract {

    protected static $map_type = Model_Map_Abstract::MMA_TYPE_LABYRINTH;

    private $mapscheme = [];
    private $grid;
    private $distance;
    private $neutral_class;
    private $entry_class;
    private $entry = [0,0];

    protected static $default_movement_cost_modifier = 0.25;

    private $location_directory = [];
    private $placement_directory = [];

    public const MML_WALL = 0;
    public const MML_CORRIDOR = 1;
    public const MML_INTERSECTION = 2;
    public const MML_FAR = 3;
    public const MML_ENTRYPOINT = 4;

    public function transport_distance_modifies(float $d): float {
        return 0;
    }

    private function valid($x = null, $y = null): bool {
        if ($x !== null && abs($x) > $this->grid) return false;
        if ($y !== null && abs($y) > $this->grid) return false;
        return true;
    }

    private function get($x, $y) {
        if (!$this->valid($x,$y)) return static::MML_WALL;
        else return $this->mapscheme[$x][$y];
    }

    private function walk($x,$y, $limit): array {
        $ret = [];
        if (!$this->mapscheme[$x][$y]) return $ret;

        $tempsheme = $this->mapscheme;

        $dir = null;
        while (count($ret) <= $limit) {
            $tmp = [];
            foreach ([[1,0],[-1,0],[0,1],[0,-1]] as $direction) {
                $tx = $x+$direction[0]; $ty = $y+$direction[1];
                if (!$this->valid($tx,$ty)) continue;
                if ($tempsheme[$tx][$ty]) continue;

                foreach ([[1,1],[-1,-1],[1,-1],[-1,1]] as $diag) {
                    $dx = $tx+$diag[0]; $dy = $ty+$diag[1];
                    /** @noinspection NotOptimalIfConditionsInspection */
                    if ($this->valid($dx,$dy) && $tempsheme[$dx][$dy] && $tempsheme[$tx][$dy] && $tempsheme[$dx][$ty])
                        continue 2;
                }
                $tmp[] = $direction;
            }

            if (empty($tmp)) return $ret;
            else {
                if (in_array($dir,$tmp,true) && random_int(0,9) < 7) {}
                elseif (empty($ret) || random_int(0,9) < 6) $dir = Tool_Gambling::select($tmp);
                else return $ret;

                $x += $dir[0]; $y += $dir[1];

                $ret[] = [$x,$y];
                $tempsheme[$x][$y] = static::MML_CORRIDOR;
            }
        }

        return $ret;
    }

    private function build_space(): void {
        for ($x = -$this->grid; $x <= $this->grid; $x++) {
            $tmp = [];
            for ($y = -$this->grid; $y <= $this->grid; $y++)
                $tmp[$y] = 0;
            $this->mapscheme[$x] = $tmp;
        }
    }

    private function build_corridors($limit): void {
        $this->entry = [random_int(0,$this->grid), random_int(0,$this->grid)];
        $this->mapscheme[$this->entry[0]][$this->entry[1]] = static::MML_ENTRYPOINT;
        $walker_points = [[$this->entry[0],$this->entry[1]]];

        $bias = 10;
        while ($limit > 0 && count($walker_points) > 0) {
            $s = [];
            for ($i = 0; $i < 3; $i++)
                $s[] = Tool_Gambling::select($walker_points);

            foreach ($s as $start) {
                $path = $this->walk($start[0], $start[1], $limit);
                $num = count($path);
                if ($num === 0 || $num >= $bias) {
                    $limit -= $num;
                    if (($p = array_search($start,$walker_points,true)) !== false) unset($walker_points[$p]);
                    if ($num > 0)
                        foreach ($path as $spot) {
                            $walker_points[] = $spot;
                            $this->mapscheme[$spot[0]][$spot[1]] = static::MML_CORRIDOR;
                        }
                }
            }

            if ($bias > 1) $bias--;
        }
    }

    private function build_intersections(): void {
        foreach ($this->mapscheme as $x => &$col)
            foreach ($col as $y => &$cell)
                if ($cell === static::MML_CORRIDOR && ($this->get($x+1,$y) || $this->get($x-1,$y)) && ($this->get($x,$y+1) || $this->get($x,$y-1)))
                    $cell = static::MML_INTERSECTION;

    }

    private function distance_rec(&$map, $rx, $ry, $d): void {
        if (!$this->valid($rx,$ry) || !$this->get($rx,$ry)) return;

        if ($map[$rx][$ry] > $d) {
            $map[$rx][$ry] = $d;
            foreach ([[1,0],[-1,0],[0,1],[0,-1]] as $dir)
                $this->distance_rec($map,$rx+$dir[0],$ry+$dir[1],$d+1);
        }
    }

    private function build_distances(): void {
        $map = [];
        for ($x = -$this->grid; $x <= $this->grid; $x++) {
            $tmp = [];
            for ($y = -$this->grid; $y <= $this->grid; $y++)
                $tmp[$y] = PHP_INT_MAX;
            $map[$x] = $tmp;
        }

        $this->distance_rec($map,$this->entry[0],$this->entry[1],0);
        for ($x = -$this->grid; $x <= $this->grid; $x++)
            for ($y = -$this->grid; $y <= $this->grid; $y++)
                if ($map[$x][$y] > 10 && $this->get($x,$y) === static::MML_CORRIDOR)
                    $this->mapscheme[$x][$y] = static::MML_FAR;

    }

    private function place_rec($x, $y, $root = null): void {
        if (!$this->valid($x,$y) || !$this->get($x,$y) || in_array([$x,$y],array_values($this->placement_directory), true)) return;

        $cls = !$root ? $this->entry_class : $this->neutral_class;
        $location = new $cls();

        $new = $this->implant_location($location, $x * $this->distance, $y * $this->distance, false, null,true,$root,true,0,!$root ? 1 : null);
        $this->update_placement_limits($cls, $root);

        $this->location_directory[$new] = $this->get($x,$y);
        $this->placement_directory[$new] = [$x,$y];

        foreach ([[1,0],[-1,0],[0,1],[0,-1]] as $direction)
            $this->place_rec($x + $direction[0], $y + $direction[1], $new);
    }

    public function auto_init(): void {
        $this->grid = (int)$this->get_local_meta($this->sublocation)['grid'];
        $this->distance = (int)$this->get_local_meta($this->sublocation)['distance'];

        $this->neutral_class = $this->get_local_meta($this->sublocation)['neutral_class'];
        $this->entry_class = $this->get_local_meta($this->sublocation)['entry_class'];

        $this->build_space();
        $this->build_corridors((int)$this->get_local_meta($this->sublocation)['size']);
        $this->build_intersections();
        $this->build_distances();

        $this->place_rec($this->entry[0],$this->entry[1]);


        $config = $this->get_config();

        //Place locations
        foreach ($config as $class => $data) if ($data['auto'] && ($data['sub'] === $this->sublocation || (is_array($data['sub']) && in_array($this->sublocation, $data['sub'], true))))
            for ($i = 0; $i < $data['num']; $i++)
                $this->place_location($class, true, $data['contortion'],
                    $data['fixed'] ?? null
                );


        $this->sub_routing->compile();
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
        $class = is_string($location) ? $location : get_class($location);

        if (!($cfg = $this->get_config($class)))
            return false;

        if (is_string($location) && $visible)
            $location = new $location;

        if (!Tool_System::instance_of($location, 'Model_Places_Abstract_Place')
            || !($cfg = &$this->get_mutable_config($location))) return false;

        $possible_targets = [];
        foreach ($this->location_directory as $id => $type)
            if (in_array($type, $cfg['root'], true)) $possible_targets[] = $id;

        if (empty($possible_targets)) return false;
        $list = $this->check_placement_limits($class,$possible_targets);
        if (!$list) return false;
        shuffle($list);

        $this->implant_location($location, 0, 0, true, null, true, $list[0], $visible,$dry,$fixed_id);
        $this->update_placement_limits($class, $list[0]);

        return true;
    }

    public function scheme(): array {
        return $this->mapscheme;
    }
}