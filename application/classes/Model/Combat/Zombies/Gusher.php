<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Combat_Zombies_Gusher extends Model_Combat_Zombies_Zombie {

    public static $custom_sprite = 'zombie_gusher.gif';
    protected static $default_weapon = 'Model_Items_Gush';

    protected $actor_name = 'Spritzer';
    protected $max_health = 1;

    protected $stat_initiative = 0;
    protected $stat_damage = 10;
    protected $stat_resistance = 0;
    protected $stat_accuracy = 10;

    protected $movement_range = 0;
    protected static $num_str = 15;

    public function idle() {
        $this->damage($this->count * $this->max_health);
        parent::idle();
    }
}