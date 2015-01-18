<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Routing {

    const grid_size = 2;

    /**
     * @var array ['x_y' => ['x' => x, 'y' => y]]
     */
    private $nodes = Array();

    private $protected_nodes = array();

    /**
     * @var array ['n1' => 'n2']
     */
    private $links = Array();

    /**
     * @var array ['n1_n2' => true/false] true means horizontal
     */
    private $direction = Array();

    private function name($x, $y, $i = 0) {
        if ($x == -0) $x = 0; if ($y == -0) $y = 0;
        return "{$x}_{$y}_{$i}";
    }

    /**
     * Places a node as close as possible to [$x/$y] without overwriting an existing node
     * @param number $x
     * @param number $y
     * @return null|string
     */
    private function place_node($x, $y) {
        $i = 0;
        $name = null;
        do {
            $name = $this->name($x, $y, $i);
            $i++;
        } while (isset($this->nodes[$name]));
        $this->nodes[$name] = Array('x' => $x, 'y' => $y);
        return $name;
    }

    /**
     * Adds a node to the map
     * @param number $x X Position
     * @param number $y Y Position
     * @param bool $protected
     * @return string Node ID
     */
    public function add_node($x, $y, $protected = false) {
        $x = round($x/static::grid_size)*static::grid_size;
        $y = round($y/static::grid_size)*static::grid_size;

        if (!$protected) {
            $name = $this->name($x,$y);
            if (!isset($this->nodes[$name]))
                $this->nodes[$name] = Array('x' => $x, 'y' => $y);
        } else {
            $name = $this->place_node($x, $y);
            $this->regsiter_protected_node($name);
        }

        return $name;
    }

    /**
     * Checks if a node is protected; if multiple nodes are given, it checks if at least one of them is protected
     * @param string|array $id Node
     * @return bool
     */
    public function check_protection_status($id) {
        if (is_array($id))
            foreach ($id as $entry)
                if (in_array($entry, $this->protected_nodes))
                    return true;
        else return in_array($id, $this->protected_nodes);
        return false;
    }

    /**
     * Protects a node
     * @param string $id Node
     */
    public function regsiter_protected_node($id) {
        if (!$this->check_protection_status($id))
            $this->protected_nodes[] = $id;
    }

    /**
     * Removes protection from a node
     * @param string $id Node
     */
    public function unregister_protected_node($id) {
        foreach (array_keys($this->protected_nodes, $id) as $key)
            unset($this->protected_nodes[$key]);
    }

    /**
     * Links both nodes together; when the nodes don't share an x or y coordinate, a third node will be inserted to link them together
     * @param string $n1 Mode 1
     * @param string $n2 Node 2
     * @return bool
     */
    public function link_nodes($n1, $n2) {
        if (!isset($this->nodes[$n1]) || !isset($this->nodes[$n2]))
            return false;

        if ($n1 == $n2)
            return true;

        if (!isset($this->links[$n1]))
            $this->links[$n1] = Array();
        if (!isset($this->links[$n2]))
            $this->links[$n2] = Array();

        if (($this->nodes[$n1]['x'] == $this->nodes[$n2]['x']) || ($this->nodes[$n1]['y'] == $this->nodes[$n2]['y'])) {
            if (!in_array($n2, $this->links[$n1])) $this->links[$n1][] = $n2;
            if (!in_array($n1, $this->links[$n2])) $this->links[$n2][] = $n1;
            $this->direction["{$n1}_{$n2}"] = $this->direction["{$n2}_{$n1}"] = ($this->nodes[$n1]['y'] == $this->nodes[$n2]['y']);
            return true;
        } elseif (abs($this->nodes[$n1]['x'] - $this->nodes[$n2]['x']) > abs($this->nodes[$n1]['y'] - $this->nodes[$n2]['y']))
            $n3 = $this->add_node($this->nodes[$n2]['x'], $this->nodes[$n1]['y'], $this->check_protection_status(array($n1, $n2)));
        else $n3 = $this->add_node($this->nodes[$n1]['x'], $this->nodes[$n2]['y'], $this->check_protection_status(array($n1, $n2)));

        return $this->link_nodes($n1, $n3) && $this->link_nodes($n3, $n2);
    }

    /**
     * Removes the link between two nodes
     * @param string $n1 Node 1
     * @param string $n2 Node 2
     */
    public function unlink_nodes($n1, $n2) {
        if (in_array($n2, $this->links[$n1]))
            foreach (array_keys($this->links[$n1], $n2) as $key)
                unset($this->links[$n1][$key]);
        if (in_array($n1, $this->links[$n2]))
            foreach (array_keys($this->links[$n2], $n1) as $key)
                unset($this->links[$n2][$key]);
        unset($this->direction["{$n1}_{$n2}"], $this->direction["{$n2}_{$n1}"]);
    }

    /**
     * Finds the optimal route to each node coming from the given node id
     * @param string $id Node ID
     * @return array
     */
    public function build_route_array($id) {
        $map = array();
        $this->rec_route(array($id),0,$id, $map);
        return $map;
    }

    public function get_network() {
        return $this->links;
    }

    /**
     * Returns all nodes
     * @return array
     */
    public function get_nodes() {
        return $this->nodes;
    }

    /**
     * Recursive routing algorithm
     * @param array $tail
     * @param number $offset
     * @param string $id
     * @param array $map
     */
    private function rec_route($tail, $offset, $id, &$map) {
        $map[$id] = array('distance' => $offset, 'tail' => $tail);

        if (!isset($this->links[$id]))
            return;

        foreach ($this->links[$id] as $to) {
            $tmp_tail = $tail;
            $distance = abs($this->nodes[$id]['x'] - $this->nodes[$to]['x']) + abs($this->nodes[$id]['y'] - $this->nodes[$to]['y']);

            if (!isset($map[$to]) || ($map[$to]['distance'] > ($distance + $offset)) || (($map[$to]['distance'] == ($distance + $offset)) && (count($tmp_tail) < count($map[$to]['tail'])))) {
                $tmp_tail[] = $to;
                $this->rec_route($tmp_tail, $distance + $offset, $to, $map);
            }
        }
    }

    /**
     * Checks if two given nodes are connected
     * @param string $n1
     * @param string $n2
     * @return bool
     */
    private function check_connection($n1, $n2) {
        if (!isset($this->nodes[$n1]) || !isset($this->nodes[$n2]))
            return false;
        if ($n1 == $n2)
            return false;
        if (!isset($this->links[$n1]) || !isset($this->links[$n2]))
            return false;
        return (in_array($n1, $this->links[$n2]) && in_array($n2, $this->links[$n1]));
    }

    /**
     * Checks if a node lies in between two other nodes
     * @param string $node
     * @param string $pnode1
     * @param string $pnode2
     * @return bool
     */
    private function check_in_between($node, $pnode1, $pnode2) {
        if ($node == $pnode1 || $node == $pnode2) return false;
        if (!isset($this->nodes[$node])) return false;
        if (!$this->check_connection($pnode1, $pnode2))
            return false;

        if ($this->direction["{$pnode1}_{$pnode2}"] && ($this->nodes[$node]['y'] == $this->nodes[$pnode1]['y']))
            return
                (min($this->nodes[$pnode1]['x'], ($this->nodes[$pnode2]['x'])) < $this->nodes[$node]['x'])
                && (max($this->nodes[$pnode1]['x'], ($this->nodes[$pnode2]['x'])) > $this->nodes[$node]['x']);
        elseif (!$this->direction["{$pnode1}_{$pnode2}"] && ($this->nodes[$node]['x'] == $this->nodes[$pnode1]['x']))
            return
                (min($this->nodes[$pnode1]['y'], ($this->nodes[$pnode2]['y'])) < $this->nodes[$node]['y'])
                && (max($this->nodes[$pnode1]['y'], ($this->nodes[$pnode2]['y'])) > $this->nodes[$node]['y']);
        else return false;
    }

    /**
     * Removes overlapping paths by converging them to a single path that contains all the nodes from the individual paths
     * @return bool
     */
    private function fix_node_breaks() {
        $ret = false;
        foreach (array_keys($this->nodes) as $node) if (!$this->check_protection_status($node))
            foreach ($this->links as $n1 => $nodes) if (!$this->check_protection_status($n1))
                foreach ($nodes as $n2) if (!$this->check_protection_status($n2))
                    if ($this->check_in_between($node, $n1, $n2)) {
                        $this->unlink_nodes($n1, $n2);
                        $this->link_nodes($n1, $node);
                        $this->link_nodes($n2, $node);
                        $ret = true;
                    }
        return $ret;
    }

    public function compile() {
        while($this->fix_node_breaks());
    }


}