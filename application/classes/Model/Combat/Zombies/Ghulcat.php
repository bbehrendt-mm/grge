<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Combat_Zombies_Ghulcat extends Model_Combat_Zombies_Ghul {

    protected static $default_weapon = 'Model_Items_Catclaw';

    public static $custom_sprite = 'ghulcat.gif';
    public static $custom_death_sprite = 'pet_ghul_dead.gif';

    protected static $default_max_health = 25;
    protected static $default_stat_initiative = 12;
    protected static $default_stat_damage = 10;
    protected static $default_stat_resistance = 5;
    protected static $default_stat_accuracy = 12;
    protected static $movement_range = 12;

    protected static $num_str = 100;

}