<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Combat_Players_Winchester extends Model_Combat_Players_Cat {
    protected $stat_initiative = 15;
    protected $stat_damage = 8;
    protected $stat_resistance = 2;
    protected $stat_accuracy = 15;
    protected $movement_range = 13;

    public static function create_linked_actor($p, $avatar = null) {
        return parent::create_linked_actor($p, 'winchester.jpg');
    }
}