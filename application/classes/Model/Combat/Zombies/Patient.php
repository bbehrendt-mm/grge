<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Combat_Zombies_Patient extends Model_Combat_Zombies_Zombie {

    public static $custom_sprite = 'zombie_patient.png';

    protected static $default_name = 'Verstörter Patient';
    protected static $default_max_health = 35;
    protected static $default_stat_initiative = 10;
    protected static $default_stat_damage = 2;
    protected static $default_stat_resistance = 2;
    protected static $default_stat_accuracy = 0;
    //protected static $movement_range = 5;

    protected static $num_str = 15;

    public static function factory(): Model_Combat_Actor {
        return parent::factory()
            ->add_weapon(new Model_Items_Hacksaw());
    }

}