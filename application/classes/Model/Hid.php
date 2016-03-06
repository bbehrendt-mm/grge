<?php

class Model_Hid {

    private $actions = array();
    private $hidden = array();

    public static function factory() {
        return new Model_Hid();
    }

    /**
     * @param string|null $description
     * @param Model_Action $action
     * @param null|string $id
     * @return Model_Hid
     */
    public function add_action($description, $action, $id = null) {
        if (!$description) {
            if ($id === null) return $this;
            else $this->hidden[] = $id;
        }

        if ($id === null)
            $id = md5("autoid@'{$description}'");

        $this->actions[$id] = array('id' => $id, 'desc' => $description, 'action' => $action);
        return $this;
    }

    /**
     * @return string[]
     */
    public function actions() {
        $tmp = [];
        foreach ($this->actions as $id => $action)
            if (array_search($id, $this->hidden) === false)
                $tmp[$id] = $action['desc'];

        return $tmp;
    }

    /**
     * @param $id
     * @param Interface_Plentity|Model_Player $player
     * @param null|Model_Player $side_player
     * @param null|mixed $argument
     * @return bool
     */
    public function perform($id, $player, $side_player = null, $argument = null) {
        if (!isset($this->actions[$id]))
            return false;

        /** @var Model_Action $action */
        $action = $this->actions[$id]['action'];
        return $action->execute($player, $side_player, $argument);
    }

    /**
     * @param $id
     * @param Interface_Plentity|Model_Player $player
     * @param null|Model_Player $side_player
     * @param null|mixed $argument
     * @return bool
     */
    public function test($id, $player, $side_player = null, $argument = null) {
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
     * @return array
     */
    public function simple_effects($p = null) {
        /**
         * @global Model_Game $game
         * @global Model_Player $player
         */
        global $player;

        if ($p === null) $p = $player;

        $tmp = [];
        foreach ($this->actions as $id => $action) {
            if (array_search($id, $this->hidden) !== false)
                continue;

            /** @var Model_Action $a */
            $a = $action['action'];

            if ($a->has_side_effect() || $a->has_requirements() || $a->denied_for($p->type())) continue;
            $tmp[$id] = $a->list_effects($p);
        }
        return $tmp;
    }

    /**
     * @param $uid
     * @param Interface_Plentity[]|null $list_of_players
     * @return array
     */
    public function convert($uid = null, $list_of_players = null) {
        /**
         * @global Model_Game $game
         * @global Model_Player $player
         */
        global $player;

        $tmp = array();
        foreach ($this->actions as $id => $action) {
            if (array_search($id, $this->hidden) !== false)
                continue;
            /** @var Model_Action $a */
            $a = $action['action'];

            if ($list_of_players === null)
                $list_of_players = [$player];

            foreach ($list_of_players as $p) {
                if ($a->denied_for($p->type()) || ($p->id() != $player->id() && ($a->has_side_effect() || !$a->allow_remote() || !$p->allow(Interface_Plentity::IC_ALLOW_ITEMS_USE)))) continue;

                $tmp[] = array_merge($a->convert_effects($p), array(
                    'description' => $action['desc'],
                    'tooltip'     => $a->description(),
                    'action' => $action['id'],
                    'popup' => $a->popup(),
                    'target' => $uid,
                    'user' => $p->id() == $player->id() ? 0 : $p->id(),
                    'escort' => $a->has_side_effect(),
                    'requires' => $a->convert_requires($p),
                    'skin' => $a->has_side_effect() ? ('multiplayer ' . $a->buttonskin()) : $a->buttonskin(),
                    'flags' => $a->flag()
                ));
            }


        }
        return $tmp;
    }
}