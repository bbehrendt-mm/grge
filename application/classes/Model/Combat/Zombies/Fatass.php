<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Combat_Zombies_Fatass extends Model_Combat_Zombies_Zombie {

    protected $name = 'Untote Fleischberge';
    protected $max_health = 15;

    protected $stat_initiative = 0;
    protected $stat_damage = 2;
    protected $stat_resistance = 6;
    protected $stat_accuracy = 0;

    protected $movement_range = 4;

    protected static $num_str = 8;

}