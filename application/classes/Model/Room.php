<?php

class Model_Room {

    protected $space;
    protected $outside;

    protected $room_name = "";

    protected $usage = "";
    protected $satisfies = ['free'];

    protected $contains = [];
    protected $used_space = 0;

    public function __construct($space = 10, $outside = false) {
        $this->space = $space;
        $this->outside = $outside;
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
        if ($new_name === null) return $this->room_name;
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
     * @param string $type
     * @return bool
     */
    public function check_room_satisfaction($type) {
        return $type == "" ? true : in_array($type, $this->satisfies);
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
                $this->contains[] = $a;

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
    }

}