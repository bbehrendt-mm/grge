<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Combat_Zombies_Patient extends Model_Combat_Zombies_Zombie {

    public static $custom_sprite = 'zombie_patient.png';

    protected $actor_name = 'Verstörter Patient';
    protected $max_health = 35;

    protected $stat_initiative = 10;
    protected $stat_damage = 2;
    protected $stat_resistance = 2;
    protected $stat_accuracy = 0;

    protected $movement_range = 5;

    protected static $num_str = 15;

    public static function factory(): Model_Combat_Actor {
        return parent::factory()
            ->add_weapon(new Model_Items_Hacksaw());
    }

}