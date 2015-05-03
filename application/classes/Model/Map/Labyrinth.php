<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Map_Labyrinth extends Model_Map_Abstract {

    private $mapscheme = [];
    private $grid;
    private $entry = [0,0];

    const MML_WALL = 0;
    const MML_CORRIDOR = 1;
    const MML_INTERSECTION = 2;
    const MML_FAR = 3;
    const MML_ENTRYPOINT = 4;

    private function valid($x = null, $y = null) {
        if ($x !== null && abs($x) > $this->grid) return false;
        if ($y !== null && abs($y) > $this->grid) return false;
        return true;
    }

    private function get($x, $y) {
        if (!$this->valid($x,$y)) return static::MML_WALL;
        else return $this->mapscheme[$x][$y];
    }

    private function walk($x,$y, $limit) {
        if (!$this->mapscheme[$x][$y]) return 0;

        $ret = [];
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
                    if ($this->valid($dx,$dy) && $tempsheme[$dx][$dy] && $tempsheme[$tx][$dy] && $tempsheme[$dx][$ty])
                        continue(2);
                }
                $tmp[] = $direction;
            }

            if (empty($tmp)) return $ret;
            else {
                if (in_array($dir,$tmp) && mt_rand(0,9) < 7) {}
                elseif (empty($ret) || mt_rand(0,9) < 6) $dir = Tool_Gambling::select($tmp);
                else return $ret;

                $x += $dir[0]; $y += $dir[1];

                $ret[] = [$x,$y];
                $tempsheme[$x][$y] = static::MML_CORRIDOR;
            }
        }

        return $ret;
    }

    private function build_space() {
        for ($x = -$this->grid; $x <= $this->grid; $x++) {
            $tmp = [];
            for ($y = -$this->grid; $y <= $this->grid; $y++)
                $tmp[$y] = 0;
            $this->mapscheme[$x] = $tmp;
        }
    }

    private function build_corridors($limit) {
        $this->entry = [mt_rand(0,$this->grid),mt_rand(0,$this->grid)];
        $this->mapscheme[$this->entry[0]][$this->entry[1]] = static::MML_ENTRYPOINT;
        $walker_points = [[$this->entry[0],$this->entry[1]]];

        $config = $this->get_config();

        $bias = 10;
        while ($limit > 0 && count($walker_points) > 0) {
            $s = [];
            for ($i = 0; $i < 3; $i++)
                $s[] = Tool_Gambling::select($walker_points);

            foreach ($s as $start) {
                $path = $this->walk($start[0], $start[1], $limit);
                if (count($path) == 0 || count($path) >= $bias) {
                    $limit -= count($path);
                    if (($p = array_search($start,$walker_points)) !== false) unset($walker_points[$p]);
                    foreach ($path as $spot) {
                        $walker_points[] = $spot;
                        $this->mapscheme[$spot[0]][$spot[1]] = static::MML_CORRIDOR;
                    }
                }
            }

            if ($bias > 1) $bias--;
        }
    }

    private function build_intersections() {
        foreach ($this->mapscheme as $x => &$col)
            foreach ($col as $y => &$cell)
                if ($cell == static::MML_CORRIDOR && ($this->get($x+1,$y) || $this->get($x-1,$y)) && ($this->get($x,$y+1) || $this->get($x,$y-1)))
                    $cell = static::MML_INTERSECTION;

    }

    private function distance_rec(&$map, $rx, $ry, $d) {
        if (!$this->valid($rx,$ry) || !$this->get($rx,$ry)) return;

        if ($map[$rx][$ry] > $d) {
            $map[$rx][$ry] = $d;
            foreach ([[1,0],[-1,0],[0,1],[0,-1]] as $dir)
                $this->distance_rec($map,$rx+$dir[0],$ry+$dir[1],$d+1);
        }
    }

    private function build_distances() {
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
                if ($map[$x][$y] > 10 && $this->get($x,$y) == static::MML_CORRIDOR)
                    $this->mapscheme[$x][$y] = static::MML_FAR;

    }

    public function auto_init() {
        $this->grid = (int)$this->get_local_meta($this->sublocation)['grid'];

        $this->build_space();
        $this->build_corridors((int)$this->get_local_meta($this->sublocation)['size']);
        $this->build_intersections();
        $this->build_distances();
    }

    public function scheme() {
        return $this->mapscheme;
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

    }

}