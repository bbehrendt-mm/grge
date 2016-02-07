<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Combat_Zombies_Hallucination extends Model_Combat_Zombies_Zombie {

    public static $custom_sprite = 'zombie_lsd.gif';
    protected $name;
    protected $max_health = 15;

    protected $stat_initiative = 0;
    protected $stat_damage = 2;
    protected $stat_resistance = 6;
    protected $stat_accuracy = 0;

    protected static $num_str = 1;

    protected $movement_range = 4;

    public function __construct() {
        parent::__construct();

        $this->name = Tool_Gambling::select(['Schnupfophanten', 'Jigsaw-Nudisten', 'Seehofer', 'Genitalmonster', 'Pokémon', 'Schwiegermütter']);
    }

    protected function get_attack_priority($friends, $foes, $weapon = null, $ignore_range = false) {
        return null;
    }

    protected function get_weapon_priority($friends, $foes) {
        return null;
    }

}