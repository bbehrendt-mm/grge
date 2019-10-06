<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Combat_Zombies_Halloween_Woodghost extends Model_Combat_Zombies_Zombie {

    protected static $default_name = 'Geist des Waldes';
    protected static $default_max_health = 1000;

    protected static $default_stat_initiative = 0;
    protected static $default_stat_damage = 0;
    protected static $default_stat_resistance = 0;
    protected static $default_stat_accuracy = 0;

    protected static $movement_range = 5;
    protected static $num_str = 25;

    protected static $default_weapon = 'Model_Items_Hoof';
    public static $custom_sprite = 'hw_woodghost.gif';

    public function __construct() {
        parent::__construct();

        $this->add_modifier('drunk', 1);
    }

    protected function damage($damage, $from = null, $armor_damage = null): void {
        parent::damage($damage * 50, $from, $armor_damage);
    }
}