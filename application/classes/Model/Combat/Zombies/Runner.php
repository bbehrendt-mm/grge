<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Combat_Zombies_Runner extends Model_Combat_Zombies_Zombie {

    public static $custom_sprite = 'zombie_ghul.gif';
    protected $name = 'Wahnsinnige Ghule';
    protected $max_health = 2;

    protected $stat_initiative = 10;
    protected $stat_damage = 5;
    protected $stat_resistance = 0;
    protected $stat_accuracy = 0;

    protected $movement_range = 8;

    protected static $num_str = 8;

}