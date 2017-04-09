<?php

class Model_Room {

    protected $space;
    protected $outside;

    protected $room_name = "";

    protected $usage = "";
    protected $satisfies = ['free'];

    protected $contains = [];
    protected $used_space = 0;

    protected $inventory;

    public function __construct($space = 10, $outside = false) {
        $this->space = $space;
        $this->outside = $outside;
        $this->inventory = new Model_Inventory();
    }

    /**
     * @return Model_Inventory
     */
    public function inventory() {
        return $this->inventory;
    }

    /**
     * @param int $space
     * @param bool $outside
     * @return Model_Room
     */
    static function factory($space = 10, $outside = false) {
        return new Model_Room($space,$outside);
    }

    /**
     * @param string|null $new_name
     * @return string
     */
    public function name($new_name = null) {
        if ($new_name === null) return $this->room_name ? $this->room_name : null;
        else return $this->room_name = mb_substr($new_name,0,16);
    }

    /**
     * @return string[]
     */
    public function get_content() {
        return $this->contains;
    }

    /**
     * @param bool $limit_free
     * @return int
     */
    public function get_space($limit_free = false) {
        if ($this->space < 0) return PHP_INT_MAX;
        return $limit_free ? max(0, $this->space - $this->used_space) : $this->space;
    }

    /**
     * @return string|null
     */
    public function get_usage() {
        return $this->usage ? $this->usage : null;
    }

    /**
     * @param string $new_usage
     * @param bool $replace_satisfiers
     * @param string[] $new_satisfiers
     */
    public function upgrade($new_usage, $replace_satisfiers, $new_satisfiers) {
        $this->usage = $new_usage;
        if ($replace_satisfiers) $this->satisfies = [];
        foreach ($new_satisfiers as $s)
            if (!$this->check_room_satisfaction($s))
                $this->satisfies[] = $s;

    }

    /**
     * @param string|string[] $type
     * @return bool
     */
    public function check_room_satisfaction($type) {
        if (is_array($type)) {
            foreach ($type as $sub) if (!$this->check_room_satisfaction($sub)) return false;
            return true;
        } else return $type == "" ? true : in_array($type, $this->satisfies);
    }

    /**
     * @return bool
     */
    public function is_outside() {
        return $this->outside;
    }

    /**
     * @param string|string[] $a
     * @return bool
     */
    public function has_content($a) {
        if (!is_array($a)) $a = [$a];
        foreach ($a as $entry)
            if (!in_array($entry,$this->contains)) return false;
        return true;
    }

    /**
     * @param string|string[] $a
     * @param int $space
     * @return bool
     */
    public function add_content($a, $space = 0) {
        if ($this->get_space(true) < $space) return false;
        if (!is_array($a)) $a = [$a];

        foreach ($a as $elem)
            if (!$this->has_content($elem))
                $this->contains[] = $elem;

        return true;
    }

    public function remove_content($a) {
        if (!is_array($a))
            $a = [$a];
        $this->upgrades = array_filter($this->contains, function($elem) use ($a) {
            return !in_array($elem, $a);
        });
    }

    public function clear() {
        $this->used_space = 0;
        $this->contains = [];
        $this->satisfies = ['free'];
        $this->room_name = "";
        $this->inventory()->grind();
    }

}