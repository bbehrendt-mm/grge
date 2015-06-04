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
        $tmp = array();
        foreach ($this->actions as $id => $action)
            if (array_search($id, $this->hidden) !== false)
                $tmp[$id] = $action['desc'];

        return $tmp;
    }

    /**
     * @param $id
     * @param Model_Player $player
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
     * @param string|null $id
     * @return bool|int
     */
    public function can($id = null) {
        return ($id === null) ? count($this->actions) : isset($this->actions[$id]);
    }

    /**
     * @param $uid
     * @return array
     */
    public function convert($uid = null) {
        /**
         * @global Model_Player $player
         * @global Model_Game $game
         */
        global $player, $game;

        $tmp = array();
        foreach ($this->actions as $id => $action) {
            if (array_search($id, $this->hidden) !== false)
                continue;
            /** @var Model_Action $a */
            $a = $action['action'];

            if ($a->has_side_effect() && !$game->config('modules.multiplayer'))
                continue;

            $tmp[] = array_merge($a->convert_effects(), array(
                'description' => $action['desc'],
                'tooltip'     => $a->description(),
                'action' => $action['id'],
                'popup' => $a->popup(),
                'target' => $uid,
                'escort' => $a->has_side_effect(),
                'requires' => $a->convert_requires(),
                'skin' => $a->has_side_effect() ? ('multiplayer ' . $a->buttonskin()) : $a->buttonskin(),
                'flags' => $a->flag()
            ));
        }
        return $tmp;
    }
}