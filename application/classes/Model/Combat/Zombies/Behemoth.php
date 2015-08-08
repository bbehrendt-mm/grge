<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Combat_Zombies_Behemoth extends Model_Combat_Zombies_Zombie {

    protected $name = 'Zombie-Behemoth';
    protected $max_health = 100;

    protected $stat_initiative = 0;
    protected $stat_damage = 20;
    protected $stat_resistance = 10;
    protected $stat_accuracy = 0;

    protected $movement_range = 3;

}