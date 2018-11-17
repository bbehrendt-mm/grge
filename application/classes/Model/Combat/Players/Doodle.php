<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Combat_Players_Doodle extends Model_Combat_Players_Dog {
    protected static $default_stat_initiative = 11;
    protected static $default_stat_damage = 4;
    //protected static $default_stat_resistance = 0;
    protected static $default_stat_accuracy = 11;
    protected static $movement_range = 10;

    public static function create_linked_actor($p, $avatar = null) {
        return parent::create_linked_actor($p, 'doodle.jpg');
    }
}