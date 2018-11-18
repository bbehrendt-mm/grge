<?php

class Model_Room {

    protected $local_id = -1;

    protected $space;
    protected $tags = [];

    protected $room_name = '';
    protected $name_fixed = false;
    protected $name_custom = false;

    protected $usage = '';
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
            if (!isset(static::$tag_info[$tag])) throw new UnexpectedValueException("Room Config Error: Unknown tag '" . $tag . "'!");
        $this->tags = $tags;
        $this->inventory = new Model_Inventory();
    }

    public function id($new_id = null): int {
        if ($new_id === null) return $this->local_id;
        else return $this->local_id = $new_id;
    }

    public function enabled($new_val = null): bool {
        if ($new_val === null) return $this->is_enabled;
        else return $this->is_enabled = $new_val;
    }

    /**
     * @return Model_Inventory
     */
    public function inventory(): \Model_Inventory
    {
        return $this->inventory;
    }

    /**
     * @param       $id
     * @param int   $space
     * @param array $tags
     *
     * @return Model_Room
     * @throws Exception
     */
    public static function factory($id, $space = 10, $tags = []): \Model_Room
    {
        $instance = new Model_Room($space,$tags);
        $instance->id($id);
        return $instance;
    }

    /**
     * @param string|null $new_name
     * @param bool        $force
     *
     * @return string
     */
    public function name($new_name = null, $force = false): ?string {
        if ($new_name === null || mb_strlen($new_name) < 2) return $this->room_name ?: null;
        else return $this->room_name = $force ? $new_name :  mb_substr($new_name,0,16);
    }

    public function name_is_fixed($s = null): bool {
        if ($s === null) return $this->name_fixed;
        else return ($this->name_fixed = $s);
    }

    public function name_is_custom($b = null): bool {
        if ($b === null) return $this->name_custom;
        else return ($this->name_custom = $b);
    }

    /**
     * @return string[]
     */
    public function get_content(): array
    {
        return $this->contains;
    }

    /**
     * @param bool $limit_free
     * @return int
     */
    public function get_space($limit_free = false): int
    {
        if ($this->space < 0) return PHP_INT_MAX;
        return $limit_free ? max(0, $this->space - $this->used_space) : $this->space;
    }

    public function deduct_space($space, $can_fail = true): bool
    {
        if ($space === 0) return true;
        if ($can_fail && $space < $this->get_space(true)) return false;
        $this->used_space += $space;
        return true;
    }

    /**
     * @return string|null
     */
    public function get_usage(): ?string
    {
        return $this->usage ?: null;
    }

    /**
     * @param string $new_usage
     * @param bool|string[] $replace_satisfiers
     * @param string[] $new_satisfiers
     */
    public function upgrade($new_usage, $replace_satisfiers, $new_satisfiers): void
    {
        $this->usage = $new_usage;
        if ($replace_satisfiers === true) $this->satisfies = [];
        elseif (is_array($replace_satisfiers)) {
            $tmp = [];
            foreach ($this->satisfies as $sat)
                if (!in_array($sat,$replace_satisfiers, false))
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
    public function check_room_satisfaction($type): bool {
        if (is_array($type)) {
            foreach ($type as $sub) if (!$this->check_room_satisfaction($sub)) return false;
            return true;
        } else return $type === '' ? true : in_array($type, $this->satisfies,false);
    }

    /**
     * @return array
     */
    public function get_tags(): array
    {
        return $this->tags;
    }

    /**
     * @param string $tag
     * @return string|null
     */
    public static function tag_info($tag): ?string
    {
        return isset(static::$tag_info[$tag]) ? __(static::$tag_info[$tag]) : null;
    }

    /**
     * @return array
     */
    public function get_friendly_tags(): array
    {
        $r = [];
        foreach ($this->tags as $tag) $r[$tag] = static::tag_info($tag);
        return $r;
    }

    /**
     * @param string|string[] $tags
     * @return bool
     */
    public function has_tag($tags): bool {
        if (is_array($tags)) {
            foreach ($tags as $tag) if (!$this->has_tag($tag)) return false;
            return true;
        } else return $tags === '' ? true : in_array($tags,$this->tags,false);
    }

    /**
     * @param string|string[] $a
     * @return bool
     */
    public function has_content($a): bool
    {
        if (!is_array($a)) $a = [$a];
        foreach ($a as $entry)
            if (!in_array($entry,$this->contains, false)) return false;
        return true;
    }

    /**
     * @param string|string[] $a
     * @param int $space
     * @return bool
     */
    public function add_content($a, $space = 0): bool
    {
        if ($this->get_space(true) < $space) return false;
        if (!is_array($a)) $a = [$a];

        foreach ($a as $elem)
            if (!$this->has_content($elem))
                $this->contains[] = $elem;

        return true;
    }

    public function remove_content($a): void
    {
        if (!is_array($a))
            $a = [$a];
        $this->contains = array_filter($this->contains, function($elem) use ($a) {
            return !in_array($elem, $a, false);
        });
    }

    public function clear(): void
    {
        $this->used_space = 0;
        $this->contains = [];
        $this->satisfies = ['free'];
        $this->room_name = '';
        $this->inventory()->grind();
    }

}