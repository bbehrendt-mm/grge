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
    public function convert($uid = null, $include_js = true) {
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

            $current = array_merge($a->convert_effects(), array(
                'description' => $action['desc'],
                'tooltip'     => $a->description(),
                'action' => $action['id'],
                'javascript' => $include_js ? ($a->convert_javascript($a->has_side_effect() ? '' :
                    Model_Javascript::factory()
                        ->close_qtip()
                        ->use_item($uid, $action['id'], $action['action']->argument() ? array('coarg' => '$arg') : null  )
                )) : null,
                'requires' => $a->convert_requires(),
                'skin' => $a->has_side_effect() ? ('multiplayer ' . $a->buttonskin()) : $a->buttonskin()
            ));

            if ($a->has_side_effect())
                if (count(Tool_Scripts::comrades()) == 0 )
                    $current['form'] = array('note' => 'Hier ist niemand, auf den du diese Aktion anwenden könntest...');
                else {
                    $current['form'] = array('buttons' => array(), 'note' => 'Bitte wähle einen anderen Spieler aus:');
                    foreach (Tool_Scripts::at_location() as $p) if ($p->id() != $player->id())
                        $current['form']['buttons'][] = array(
                            'text' => $p->name(),
                            'javascript' => $include_js ?
                                Model_Javascript::factory()
                                    ->close_qtip()
                                    ->use_item($uid, $action['id'], $action['action']->argument() ? array('coarg' => '$arg', 'side' => $p->id()) : array('side' => $p->id()))
                                    ->compile()
                                : null
                        );
                }

            $tmp[] = $current;
        }
        return $tmp;
    }
}