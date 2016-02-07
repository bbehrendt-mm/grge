<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Combat_Zombies_Mutant extends Model_Combat_Zombies_Starver {

    public static $custom_sprite = 'zombie_mutant.png';

    protected $name = 'Untotes Strahlenopfer';
    protected $max_health = 2;

    protected $movement_range = 5;

    protected static $num_str = 1;

}