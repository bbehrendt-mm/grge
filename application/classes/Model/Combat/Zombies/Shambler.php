<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Combat_Zombies_Shambler extends Model_Combat_Zombies_Zombie {

    protected static $default_name = 'Vermodernde Zombies';
    protected static $default_max_health = 4;
    protected static $default_stat_initiative = 2;
    protected static $default_stat_damage = 2;
    protected static $default_stat_resistance = 2;
    protected static $default_stat_accuracy = 2;
    //protected static $movement_range = 5;

    protected static $num_str = 2;

}