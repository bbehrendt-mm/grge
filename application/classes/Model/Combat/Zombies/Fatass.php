<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Combat_Zombies_Fatass extends Model_Combat_Zombies_Zombie {

    public static $custom_sprite = 'zombie_fatso.gif';

    protected static $default_name = 'Untote Fleischberge';
    protected static $default_max_health = 17;
    protected static $default_stat_initiative = 0;
    protected static $default_stat_damage = 2;
    protected static $default_stat_resistance = 6;
    protected static $default_stat_accuracy = 0;
    protected static $movement_range = 4;

    protected static $num_str = 8;

}