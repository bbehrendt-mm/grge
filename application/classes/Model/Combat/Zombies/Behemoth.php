<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Combat_Zombies_Behemoth extends Model_Combat_Zombies_Zombie {

    public static $custom_sprite = 'zombie_behemoth.gif';
    public static $custom_death_sprite = 'zombie_behemoth_dead.gif';

    protected static $default_name = 'Zombie-Behemoth';
    protected static $default_max_health = 100;

    protected static $default_stat_initiative = 0;
    protected static $default_stat_damage = 20;
    protected static $default_stat_resistance = 10;
    protected static $default_stat_accuracy = 0;

    protected static $movement_range = 3;

    protected static $num_str = 60;

}