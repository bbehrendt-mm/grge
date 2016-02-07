<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Combat_Zombies_Behemoth extends Model_Combat_Zombies_Zombie {

    public static $custom_sprite = 'zombie_behemoth.gif';
    public static $custom_death_sprite = 'zombie_behemoth_dead.gif';

    protected $name = 'Zombie-Behemoth';
    protected $max_health = 100;

    protected $stat_initiative = 0;
    protected $stat_damage = 20;
    protected $stat_resistance = 10;
    protected $stat_accuracy = 0;

    protected $movement_range = 3;

    protected static $num_str = 100;

}