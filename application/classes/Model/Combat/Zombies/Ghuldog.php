<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Combat_Zombies_Ghuldog extends Model_Combat_Zombies_Ghul {

    protected static $default_weapon = 'Model_Items_Dogbite';

    public static $custom_sprite = 'ghuldog.gif';
    public static $custom_death_sprite = 'pet_ghul_dead.gif';

    protected static $default_max_health = 50;
    protected static $default_stat_initiative = 10;
    protected static $default_stat_damage = 4;
    protected static $default_stat_resistance = 0;
    protected static $default_stat_accuracy = 10;
    protected static $movement_range = 10;

    protected static $num_str = 90;

}