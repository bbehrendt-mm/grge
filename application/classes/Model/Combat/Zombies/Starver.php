<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Combat_Zombies_Starver extends Model_Combat_Zombies_Zombie {

    public static $custom_sprite = 'zombie_starver.gif';

    protected static $default_name = 'Hungerer';
    protected static $default_max_health = 1;

    protected static $default_stat_initiative = 0;
    protected static $default_stat_damage = 0;
    protected static $default_stat_resistance = 0;
    protected static $default_stat_accuracy = 0;
    protected static $movement_range = 1;
}