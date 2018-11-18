<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Combat_Players_Winchester extends Model_Combat_Players_Cat {
    protected static $default_stat_initiative = 15;
    protected static $default_stat_damage = 8;
    protected static $default_stat_resistance = 2;
    protected static $default_stat_accuracy = 15;
    protected static $movement_range = 13;

    public static function create_linked_actor($p, $avatar = null): Model_Combat_Players_Player
    {
        return parent::create_linked_actor($p, 'winchester.jpg');
    }
}