<?php

class Model_NPC_Special_Winchester extends Model_NPC_Cat
{
    protected static $movement_scaling = 0.5;
    protected static $alcohol_scaling = 10;
    protected static $inventory_size = 5;
    protected static $comfort_threshold = 60;

    public function __construct() {
        parent::__construct('Winchester');

        $this->get_status()->set(
            Model_Status::MS_STAT_HEALTH, mt_rand(90,100),
            Model_Status::MS_STAT_ENERGY, mt_rand(90,100),
            Model_Status::MS_STAT_HUNGER, mt_rand(90,100),
            Model_Status::MS_STAT_THIRST, mt_rand(90,100),
            Model_Status::MS_STAT_SLEEPY, 100
        );
    }

    public function create_combatant() {
        return Model_Combat_Players_Winchester::create_linked_actor($this);
    }

    public function entity_description() {
        return 'Du hast Winchester in einer Bar gefunden und es nicht übers Herz gebracht, ihn dort einfach alleine zurückzulassen.';
    }
}