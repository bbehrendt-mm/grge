<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Combat_Zombies_Runner extends Model_Combat_Zombies_Zombie {

    public static $custom_sprite = 'zombie_ghul.gif';

    protected static $default_name = 'Wahnsinnige Ghule';
    protected static $default_max_health = 2;
    protected static $default_stat_initiative = 10;
    protected static $default_stat_resistance = 0;
    protected static $default_stat_accuracy = 0;
    protected static $movement_range = 8;

    protected static $num_str = 8;

}