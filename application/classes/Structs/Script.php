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