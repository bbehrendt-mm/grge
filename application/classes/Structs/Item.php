<?php
/**
 * Created by PhpStorm.
 * User: Benjamin
 * Date: 04.11.2018
 * Time: 16:26
 */

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
}

class Struct_ItemMaterial extends Struct_ItemEntry {
    /**
     * @var null|callable $decider
     */
    public $decider;
}