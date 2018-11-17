<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Combat_Zombies_Gusher extends Model_Combat_Zombies_Zombie {

    public static $custom_sprite = 'zombie_gusher.gif';
    protected static $default_weapon = 'Model_Items_Gush';

    protected static $default_name = 'Spritzer';
    protected static $default_max_health = 1;
    protected static $default_stat_initiative = 0;
    protected static $default_stat_damage = 10;
    protected static $default_stat_resistance = 0;
    protected static $default_stat_accuracy = 10;
    protected static $movement_range = 0;

    protected static $num_str = 15;

    public function idle(): void {
        $this->damage($this->c_count * $this->max_health);
        parent::idle();
    }
}