<?php

class Model_NPC_Special_Doodle extends Model_NPC_Dog
{
    protected static $movement_scaling = 0.5;
    protected static $alcohol_scaling = 5;
    protected static $inventory_size = 50;
    protected static $comfort_threshold = 30;

    protected static $namelist = ['Larry','Morton','Wendy','Iggy','Roy','Lemmy','Ludwig'];

    public function __construct($level) {
        parent::__construct();

        $this->inventory()->limit(30 + 10 * $level);

        $this->get_status()->set(
            Model_Status::MS_STAT_HEALTH, 100,
            Model_Status::MS_STAT_ENERGY, 100,
            Model_Status::MS_STAT_HUNGER, 100,
            Model_Status::MS_STAT_THIRST, 100,
            Model_Status::MS_STAT_SLEEPY, 100
        );
    }

    public function create_combatant() {
        return Model_Combat_Players_Doodle::create_linked_actor($this);
    }

    public function entity_description() {
        return 'Dein treuer, vierbeiniger Freund steht dir auch in der Postapokalypse treu zur Seite.';
    }

    public function icon() {
        return 'doodle.gif';
    }
}