<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Combat_Players_Saint extends Model_Combat_Players_Player {

    public static $custom_sprite = 'saint.gif';

    public function __construct() {
        parent::__construct();
        $this->current_weapon = new Model_Items_Godsword();
    }

    protected function damage($damage, $from = null, $armor_damage = null) {
        parent::damage(0, $from, 0);
    }

    protected function get_weapon_priority($friends, $foes) {
        return null;
    }
}