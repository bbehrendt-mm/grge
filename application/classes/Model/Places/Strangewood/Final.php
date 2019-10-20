<?php defined('SYSPATH') OR die('No direct access allowed.');

abstract class Model_Places_Strangewood_Final extends Model_Places_Abstract_Trap {

    protected $unlocked = false;
    protected $pid = null;
    protected $profession = null;

    protected static $icon = 'swood';

    protected function unlock() {
        $this->unlocked = true;
    }

    abstract protected function initialize();

    abstract protected function name_by_profession($p) : string;
    abstract protected function desc_by_profession($p) : string;
    abstract protected function out_by_profession($p) : bool;

    public function name() {
        return $this->name_by_profession($this->profession);
    }

    public function description() {
        return $this->desc_by_profession($this->profession);
    }

    public function is_outside(): bool {
        return $this->out_by_profession($this->profession);
    }

    public function uin($uin = NULL) {
        $t = parent::uin($uin);
        if ($uin !== null) $this->initialize();
        return $t;
    }

    public function set_owner(Model_Player $p) {
        $this->pid = $p->id();
        $this->profession = $p->job();
    }

    public function __construct(Model_Player $p)
    {
        $this->set_owner($p);
        parent::__construct();
    }

    //Leave location
    public function can_leave($pid = null, $ignore_zombies = false, $type = Interface_Tickable::IT_TYPE_PLAYER): bool
    {
        if ($type === Interface_Tickable::IT_TYPE_PLAYER && !$this->unlocked)
            Globals::CurrentPlayerActualF()->log()->add('Es sieht nicht so aus, als ob du diesen Ort im Moment verlassen kannst... Vielleicht solltest du dich einmal hier umsehen?');

        return $this->unlocked && parent::can_leave($pid,$ignore_zombies,$type);
    }

    //Enter location
    public function can_enter($pid = null, $type = Interface_Tickable::IT_TYPE_PLAYER): bool
    {
        $blocked = $this->pid !== null && $pid !== $this->pid && !Tool_Scripts::is_npc(Globals::CurrentGameF()->get_player($pid));
        if ($type === Interface_Tickable::IT_TYPE_PLAYER && $blocked)
            Globals::CurrentPlayerActualF()->log()->add('Du spürst, dass dieser Ort nur nach :player ruft.', Globals::CurrentGameF()->get_player($pid)->name());

        return !$blocked && parent::can_enter($pid,$type);
    }

}