<?php

class Model_Npc extends Model_Hid {

    private $name;

    /**
     * @return Model_Hid|Model_Npc
     */
    public static function factory() {
        return new Model_Npc();
    }

    public function __construct($name = 'NPC') {
        $this->name = $name;
    }

    /**
     * @param null $new
     * @return Model_Npc|string
     */
    public function name($new = null) {
        if ($new === null) return $this->name;
        else {
            $this->name = $new;
            return $this;
        }
    }
}