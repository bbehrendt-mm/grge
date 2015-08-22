<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Combat_Zombies_Patient extends Model_Combat_Zombies_Zombie {

    protected $name = 'Verstörter Patient';
    protected $max_health = 35;

    protected $stat_initiative = 10;
    protected $stat_damage = 2;
    protected $stat_resistance = 2;
    protected $stat_accuracy = 0;

    protected $movement_range = 5;

    public static function factory() {
        return parent::factory()
            ->add_weapon(new Model_Items_Hacksaw());
    }

}