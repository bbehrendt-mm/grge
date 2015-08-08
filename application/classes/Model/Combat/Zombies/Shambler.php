<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Combat_Zombies_Shambler extends Model_Combat_Zombies_Zombie {

    protected $name = 'Vermodernde Zombies';
    protected $max_health = 3;

    protected $stat_initiative = 2;
    protected $stat_damage = 2;
    protected $stat_resistance = 2;
    protected $stat_accuracy = 2;

    protected $movement_range = 5;

}