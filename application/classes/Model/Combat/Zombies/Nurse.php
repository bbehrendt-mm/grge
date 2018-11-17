<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Combat_Zombies_Nurse extends Model_Combat_Zombies_Shambler {

    public static $custom_sprite = 'zombie_nurse.png';

    protected static $default_name = 'Untote sexy Krankenschwester';
    protected static $default_max_health = 6;
    protected static $movement_range = 4;

    protected static $num_str = 3;

}