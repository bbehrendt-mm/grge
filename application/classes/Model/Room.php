<?php

class Model_Room {

    protected $local_id = -1;

    /** @var null|Model_Room */
    protected $default_room = null;

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

    protected $defense = 0.0;
    protected $deco = 0.0;

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

    public function defense(): float {
        return $this->defense;
    }

    public function set_defense(float $val): void {
        $this->defense = $val;
    }

    public function modify_defense(float $val): float {
        return $this->defense += $val;
    }

    public function deco(): float {
        return $this->deco;
    }

    public function set_deco(float $val): void {
        $this->deco = $val;
    }

    public function modify_deco(float $val): float {
        return $this->deco += $val;
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
     * @return string
     */
    public function name($new_name = null, $force = false): ?string {
        if ($new_name === null || (mb_strlen($new_name) < 2 && $new_name !== '')) return $this->room_name ?: null;
        else if ($new_name === '') return $this->room_name = null;
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

    public function get_satisfaction(): array {
        return $this->satisfies;
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
        if ($can_fail && $space > $this->get_space(true)) return false;
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
        $this->usage = $new_usage ?: $this->usage;
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
     * @param string|string[] $tags
     */
    public function remove_tag($tags): void {
        if (is_array($tags)) foreach ($tags as $tag) $this->remove_tag($tag);
        else $this->tags = array_filter($this->tags, function($tag) use ($tags) { return $tag !== $tags; });
    }

    /**
     * @param string|string[] $tags
     */
    public function add_tag($tags): void {
        if (is_array($tags)) foreach ($tags as $tag) $this->add_tag($tag);
        else if (!$this->has_tag($tags)) $this->tags[] = $tags;
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

    public function reset_default_state(): void {
        $this->default_room = null;
    }

    public function set_default_state(): void {
        $this->default_room = new Model_Room();

        $this->default_room->space = $this->space;
        $this->default_room->tags  = $this->tags;

        $this->default_room->room_name    = $this->room_name;
        $this->default_room->name_custom  = $this->name_custom;

        $this->default_room->usage      = $this->usage;
        $this->default_room->satisfies  = $this->satisfies;

        $this->default_room->contains    = $this->contains;
        $this->default_room->used_space  = $this->used_space;

        $this->default_room->defense  = $this->defense;
        $this->default_room->deco     = $this->deco;

        foreach ($this->inventory()->get() as $item)
            $this->default_room->inventory->add( $item );
    }

    /**
     * @return Model_Items_Abstract_Item[]
     */
    public function revert_default_state(): array {
        if (!$this->has_explicit_default()) return [];

        $this->space = $this->default_room->space;
        $this->tags  = $this->default_room->tags;

        $this->room_name    = $this->default_room->room_name;
        $this->name_custom  = $this->default_room->name_custom;

        $this->usage      = $this->default_room->usage;
        $this->satisfies  = $this->default_room->satisfies;

        $this->contains    = $this->default_room->contains;
        $this->used_space  = $this->default_room->used_space;

        $this->defense  = $this->default_room->defense;
        $this->deco     = $this->default_room->deco;

        $discard_item_list = [];
        foreach ($this->inventory->get(Model_Items_Abstract_Item::cls()) as $item)
            if (!$this->default_room->inventory->has( $item->uin() ))
                $discard_item_list[] = $this->inventory->remove( $item->uin() );

        return $discard_item_list;
    }

    public function has_explicit_default(): bool {
        return $this->default_room !== null;
    }

    public function has_different_default(): bool {
        if (!$this->has_explicit_default()) return false;
        return
            !empty(array_diff( $this->default_room->satisfies, $this->satisfies )) ||
            !empty(array_diff( $this->satisfies, $this->default_room->satisfies )) ||
            !empty(array_diff( $this->default_room->tags, $this->tags )) ||
            !empty(array_diff( $this->tags, $this->default_room->tags )) ||
            $this->usage != $this->default_room->usage || $this->used_space != $this->default_room->used_space;
    }

    public function clear(): void
    {
        $this->used_space = 0;
        $this->contains = [];
        $this->satisfies = ['free'];
        $this->room_name = '';
        $this->usage = '';
        $this->deco = 0;
        $this->defense = 0;
        $this->inventory()->grind();
    }

}