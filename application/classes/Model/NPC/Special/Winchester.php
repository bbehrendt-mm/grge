<?php

class Model_NPC_Special_Winchester extends Model_NPC_Cat
{
    protected static $movement_scaling = 0.5;
    protected static $comfort_threshold = 60;

    public function __construct() {
        parent::__construct('Winchester');

        $this->get_status()->set(
            Model_Status::MS_STAT_HEALTH, random_int(90,100),
            Model_Status::MS_STAT_ENERGY, random_int(90,100),
            Model_Status::MS_STAT_HUNGER, random_int(90,100),
            Model_Status::MS_STAT_THIRST, random_int(90,100),
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