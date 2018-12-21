<?php

class Model_Hid {

    private $actions = array();
    private $hidden = array();
    private $base_id;
    private $parent;

    public static function factory(Model_Items_Abstract_Item $parent = null, $bs = null): \Model_Hid
    {
        return new Model_Hid($parent, $bs);
    }

    public function __construct(Model_Items_Abstract_Item $parent = null, $bs = null) {
        $this->base_id = $bs ?: 'functional';
        $this->parent = $parent;
    }

    /**
     * @param string|null $description
     * @param Model_Action $action
     * @param null|string $id
     * @return Model_Hid
     */
    public function add_action($description,Model_Action $action, $id = null): \Model_Hid
    {
        if (!$description) {
            if ($id === null) return $this;
            else $this->hidden[] = $id;
        }

        if ($id === null)
            $id = md5("autoid.{$this->base_id}@'{$description}'");

        if ($this->parent) $action->setParent($this->parent);
        $this->actions[$id] = array('id' => $id, 'desc' => $description, 'action' => $action);
        return $this;
    }

    /**
     * @param string $id
     * @param bool $use_auto_id
     * @return Model_Action|null
     */
    public function &get_action($id, $use_auto_id = false): ?\Model_Action
    {
        global $null;
        $null = null;

        if ($use_auto_id) $id = md5("autoid.{$this->base_id}@'{$id}'");
        if (isset($this->actions[$id])) return $this->actions[$id];
        else return $null;
    }

    /**
     * @return string[]
     */
    public function actions(): array
    {
        $tmp = [];
        foreach ($this->actions as $id => $action)
            if (!in_array($id, $this->hidden, true))
                $tmp[$id] = $action['desc'];

        return $tmp;
    }

    /**
     * @param                                 $id
     * @param Interface_Plentity|Model_Player $player
     * @param null|Model_Player               $side_player
     * @param null|mixed                      $argument
     *
     * @return bool
     * @throws Exception
     */
    public function perform($id, $player, $side_player = null, $argument = null): bool
    {
        if (!isset($this->actions[$id]))
            return false;

        /** @var Model_Action $action */
        $action = $this->actions[$id]['action'];
        if (Globals::hasCurrentGame())
            foreach (Globals::CurrentGameF()->get_initialized_events() as $ev)
                $ev->event_executeHIDAction($this->base_id, $this->actions[$id]['desc'], $action);
        return $action->execute($player, $side_player, $argument);
    }

    /**
     * @param                                 $id
     * @param Interface_Plentity|Model_Player $player
     * @param null|Model_Player               $side_player
     * @param null|mixed                      $argument
     * @return bool
     * @throws Exception
     */
    public function test($id, $player, $side_player = null, $argument = null): bool
    {
        if (!isset($this->actions[$id]))
            return false;

        /** @var Model_Action $action */
        $action = $this->actions[$id]['action'];
        return $action->test($player, $side_player, $argument);
    }

    /**
     * @param string|null $id
     * @return bool|int
     */
    public function can($id = null) {
        return ($id === null) ? count($this->actions) : isset($this->actions[$id]);
    }

    /**
     * @param Interface_Plentity $p
     * @param bool               $auto
     * @return array
     * @throws Exception
     */
    public function simple_effects($p = null, $auto = false): array
    {
        if ($p === null) $p = Globals::CurrentPlayerF();

        $tmp = [];
        foreach ($this->actions as $id => $action) {
            if (in_array($id, $this->hidden, true))
                continue;

            /** @var Model_Action $a */
            $a = $action['action'];

            if (($auto && !$a->allow_auto()) || $a->has_side_effect() || $a->has_requirements() || $a->denied_for($p->type())) continue;
            $tmp[$id] = $a->list_effects($p);
        }
        return $tmp;
    }

    /**
     * @param                           $uid
     * @param Interface_Plentity[]|null $list_of_players
     * @return array
     * @throws Exception
     */
    public function convert($uid = null, $list_of_players = null): array
    {
        $tmp = array();
        foreach ($this->actions as $id => $action) {
            if (in_array($id, $this->hidden, true))
                continue;
            /** @var Model_Action $a */
            $a = $action['action'];

            foreach (Globals::CurrentGameF()->get_initialized_events() as $ev)
                $ev->event_renderHIDAction($this->base_id, $action['desc'], $a);

            if ($list_of_players === null)
                $list_of_players = [Globals::CurrentPlayerF()];

            foreach ($list_of_players as $p) {
                if ($a->denied_for($p->type()) || ($p->id() !== Globals::CurrentPlayerF()->id() && ($a->has_side_effect() || !$a->allow_remote() || !$p->allow(Interface_Plentity::IC_ALLOW_ITEMS_USE)))) continue;

                $tmp[] = array_merge($a->convert_effects($p), array(
                    'description' => $action['desc'],
                    'tooltip'     => $a->description(),
                    'action' => $action['id'],
                    'popup' => $a->popup(),
                    'target' => $uid,
                    'user' => $p->id() === Globals::CurrentPlayerF()->id() ? 0 : $p->id(),
                    'escort' => $a->has_side_effect(),
                    'requires' => $a->convert_requires($p),
                    'skin' => $a->has_side_effect() ? ('multiplayer ' . $a->buttonskin()) : $a->buttonskin(),
                    'flags' => $a->get_flag()
                ));
            }


        }
        return $tmp;
    }
}