<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Combat_Zombies_Fatass extends Model_Combat_Zombies_Zombie {

    public static $custom_sprite = 'zombie_fatso.gif';

    protected $actor_name = 'Untote Fleischberge';
    protected $max_health = 17;

    protected $stat_initiative = 0;
    protected $stat_damage = 2;
    protected $stat_resistance = 6;
    protected $stat_accuracy = 0;

    protected $movement_range = 4;

    protected static $num_str = 8;

}