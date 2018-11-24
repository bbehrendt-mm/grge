<?php
class Struct_ItemEntry extends Struct {
    /**
     * @var string $class
     */
    public $class;

    /**
     * @var int $count
     */
    public $count = 1;

    public $type;

    public function name(): string
    {
        $class = $this->class;
        /** @var Model_Items_Abstract_Item $class */
        return $class::static_name($this->type);
    }

    public function desc(): string
    {
        $class = $this->class;
        /** @var Model_Items_Abstract_Item $class */
        return $class::static_description($this->type);
    }

    public function icon(): string
    {
        $class = $this->class;
        /** @var Model_Items_Abstract_Item $class */
        return $class::static_icon($this->type);
    }

    public static function make(string $classname, int $count = 1, ?int $type = null): self {
        $instance = new self();
        $instance->class = $classname;
        $instance->count = $count;
        $instance->type = $type;
        return $instance;
    }

    /**
     * @param array $matrix
     *
     * @return Struct_ItemEntry[]
     */
    public static function convert(array $matrix): array {
        $ret = [];
        foreach ($matrix as $cls => $cnt)
            $ret[] = self::make($cls,$cnt);

        return $ret;
    }
}

class Struct_ItemMaterial extends Struct_ItemEntry {
    /**
     * @var null|callable $decider
     */
    public $decider;

    public function get_decider(): callable {
        return $this->decider ?? function(Model_Items_Abstract_Item $item):bool { return true; };
    }
}