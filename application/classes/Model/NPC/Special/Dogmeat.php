<?php

class Model_NPC_Special_Dogmeat extends Model_NPC_Dog
{
    protected static $movement_scaling = 0.5;
    protected static $alcohol_scaling = 5;
    protected static $inventory_size = 20;
    protected static $comfort_threshold = 30;

    public function __construct() {
        parent::__construct('Dogmeat');

        $this->get_status()->set(
            Model_Status::MS_STAT_HEALTH, random_int(75,90),
            Model_Status::MS_STAT_ENERGY, random_int(80,100),
            Model_Status::MS_STAT_HUNGER, random_int(50,90),
            Model_Status::MS_STAT_THIRST, random_int(55,90),
            Model_Status::MS_STAT_SLEEPY, 100
        );
    }

    public function create_combatant() {
        return Model_Combat_Players_Dogmeat::create_linked_actor($this);
    }

    public function entity_description() {
        return 'Dogmeat ist dir in der Umgebung deines Verstecks zugelaufen und weicht dir seither nicht mehr von der Seite.';
    }
}