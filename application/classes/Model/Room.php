<?php

class Model_Room {

    protected $local_id = -1;

    protected $space;
    protected $tags = [];

    protected $room_name = "";
    protected $name_fixed = false;
    protected $name_custom = false;

    protected $usage = "";
    protected $satisfies = ['free'];

    protected $contains = [];
    protected $used_space = 0;

    protected $inventory;

    protected $is_enabled = true;

    protected static $tag_info = [
        'inside' => 'Innen',
        'outside' => 'Außen',
        'primary' => 'Hauptraum'
    ];

    public function __construct($space = 10, $tags = []) {
        $this->space = $space;
        foreach ($tags as $tag)
            if (!isset(static::$tag_info[$tag])) throw new Exception("Room Config Error: Unknown tag '" . $tag . "'!");
        $this->tags = $tags;
        $this->inventory = new Model_Inventory();
    }

    public function id($new_id = null) {
        if ($new_id === null) return $this->local_id;
        else $this->local_id = $new_id;
    }

    public function enabled($new_val = null) {
        if ($new_val === null) return $this->is_enabled;
        else return $this->is_enabled = $new_val;
    }

    /**
     * @return Model_Inventory
     */
    public function inventory() {
        return $this->inventory;
    }

    /**
     * @param int $space
     * @param array $tags
     * @return Model_Room
     */
    static function factory($id, $space = 10, $tags = []) {
        $instance = new Model_Room($space,$tags);
        $instance->id($id);
        return $instance;
    }

    /**
     * @param string|null $new_name
     * @return string
     */
    public function name($new_name = null, $force = false) {
        if ($new_name === null || mb_strlen($new_name) < 2) return $this->room_name ? $this->room_name : null;
        else return $this->room_name = $force ? $new_name :  mb_substr($new_name,0,16);
    }

    public function name_is_fixed($s = null) {
        if ($s === null) return $this->name_fixed;
        else return ($this->name_fixed = $s);
    }

    public function name_is_custom($b = null) {
        if ($b === null) return $this->name_custom;
        else return ($this->name_custom = $b);
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
     * @param bool|string[] $replace_satisfiers
     * @param string[] $new_satisfiers
     */
    public function upgrade($new_usage, $replace_satisfiers, $new_satisfiers) {
        $this->usage = $new_usage;
        if ($replace_satisfiers === true) $this->satisfies = [];
        elseif (is_array($replace_satisfiers)) {
            $tmp = [];
            foreach ($this->satisfies as $sat)
                if (!in_array($sat,$replace_satisfiers))
                    $tmp[] = $sat;
            $this->satisfies = $tmp;
        }
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
     * @return array
     */
    public function get_tags() {
        return $this->tags;
    }

    /**
     * @param string $tag
     * @return string|null
     */
    public static function tag_info($tag) {
        return isset(static::$tag_info[$tag]) ? __(static::$tag_info[$tag]) : null;
    }

    /**
     * @return array
     */
    public function get_friendly_tags() {
        $r = [];
        foreach ($this->tags as $tag) $r[$tag] = static::tag_info($tag);
        return $r;
    }

    /**
     * @param string $tag
     * @return bool
     */
    public function has_tag($tag) {
        return in_array($tag,$this->tags);
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