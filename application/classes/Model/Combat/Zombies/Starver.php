<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Combat_Zombies_Starver extends Model_Combat_Zombies_Zombie {

    protected $name = 'Hungerer';
    protected $max_health = 1;

    protected $stat_initiative = 0;
    protected $stat_damage = 0;
    protected $stat_resistance = 0;
    protected $stat_accuracy = 0;

    protected $movement_range = 1;
    protected static $num_str = 1;

}