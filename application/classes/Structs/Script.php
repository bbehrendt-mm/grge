<?php
class Struct_ScriptItemSource extends Struct {
    /** @var bool $from_player */
    public $from_player = true;

    /** @var bool $from_location */
    public $from_location = true;

    /** @var bool $from_others */
    public $from_others = false;

    /** @var null|Interface_Plentity $perspective */
    public $perspective;

    /** @var null|callable $decider */
    protected $decider;

    public function copy(): self { return clone $this; }

    /**
     * @param bool $b
     *
     * @return Struct_ScriptItemSource
     */
    public function take_from_player(bool $b): self { $this->from_player = $b; return $this; }

    /**
     * @param bool $b
     *
     * @return Struct_ScriptItemSource
     */
    public function take_from_location(bool $b): self { $this->from_location = $b; return $this; }

    /**
     * @param bool $b
     *
     * @return Struct_ScriptItemSource
     */
    public function take_from_others(bool $b): self { $this->from_others = $b; return $this; }

    /**
     * @param Interface_Plentity|null $p
     *
     * @return Struct_ScriptItemSource
     */
    public function use_perspective(?Interface_Plentity $p): self { $this->perspective = $p; return $this; }

    /**
     * @param callable|null $f
     *
     * @return Struct_ScriptItemSource
     */
    public function use_decider(?callable $f): self { $this->decider = $f; return $this; }

    /**
     * @return callable
     */
    public function get_decider(): callable { return $this->decider ?? function(?Model_Items_Abstract_Item $item) { return $item !== null; }; }

    /**
     * @return Interface_Plentity
     * @throws Exception
     */
    public function get_player(): Interface_Plentity { return $this->perspective ?? Globals::CurrentPlayerF(); }

    public static function default():      self { return new self(); }
    public static function onlyLocation(): self { return self::default()->take_from_player(false); }
    public static function onlyPlayer():   self { return self::default()->take_from_location(false); }

}

class Struct_ScriptEffect extends Struct {
    /** @var string|null $id */
    public $id;

    /** @var Model_Effect $effect */
    public $effect;

    /** @var Model_Effect|null $effect */
    public $side_effect;

    /** @var callable|null $condition */
    public $condition;

    public function get_condition(): callable {
        return $this->condition ?? function(Interface_Plentity $p, ?Interface_Plentity $p2, $arg): bool { return true; };
    }

    public static function make(Model_Effect $effect, ?string $id = null, ?callable $condition = null, ?Model_Effect $side_effect = null): self {
        $instance = new self();
        $instance->effect = $effect;
        $instance->id = $id;
        $instance->condition = $condition;
        $instance->side_effect = $side_effect;
        return $instance;
    }
}