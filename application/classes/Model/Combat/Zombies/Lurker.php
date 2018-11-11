<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Combat_Zombies_Lurker extends Model_Combat_Zombies_Zombie {

    public static $custom_sprite = 'zombie_lurker.gif';
    protected $actor_name = 'Faulende Patienten';
    protected $max_health = 5;

    protected static $num_str = 4;

    protected $stat_initiative = 5;
    protected $stat_damage = 5;
    protected $stat_resistance = 0;
    protected $stat_accuracy = 0;

    protected $movement_range = 5;

}