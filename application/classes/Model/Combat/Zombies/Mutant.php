<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Combat_Zombies_Mutant extends Model_Combat_Zombies_Starver {

    public static $custom_sprite = 'zombie_mutant.png';

    protected static $default_name = 'Untotes Strahlenopfer';
    protected static $default_max_health = 2;
    protected static $default_movement_range = 5;
}