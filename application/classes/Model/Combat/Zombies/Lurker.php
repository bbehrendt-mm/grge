<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Combat_Zombies_Lurker extends Model_Combat_Zombies_Zombie {

    public static $custom_sprite = 'zombie_lurker.gif';

    protected static $default_name = 'Faulende Patienten';
    protected static $default_max_health = 5;
    //protected static $default_stat_initiative = 5;
    //protected static $default_stat_damage = 5;
    protected static $default_stat_resistance = 0;
    protected static $default_stat_accuracy = 0;
    //protected static $movement_range = 5;

    protected static $num_str = 4;
}