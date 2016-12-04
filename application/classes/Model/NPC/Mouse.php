<?php

class Model_NPC_Mouse extends Model_NPC_Animal
{
    protected static $escort_functions = [
        Interface_Plentity::IC_ALLOW_SHOW_INVENTORY, Interface_Plentity::IC_ALLOW_ITEM_DROP,
        Interface_Plentity::IC_ALLOW_ITEMS_SIDEUSE, Interface_Plentity::IC_ALLOW_ITEMS_USE,
        Interface_Plentity::IC_ALLOW_MOVE, Interface_Plentity::IC_ALLOW_MANAGE_ACTIVITY
    ];

    protected $last_hideout = null;

    protected static $movement_scaling = 0.15;
    protected static $alcohol_scaling = 100;
    protected static $inventory_size = 1;
    protected static $comfort_threshold = 95;
    
    protected static $abillities = [
        Interface_Plentity::IC_TRIGGER_ITEM_TICKS,
        Interface_Plentity::IC_TRIGGER_ITEM_FINDINGS,
        Interface_Plentity::IC_TRIGGER_LOCATION_TICKS
    ];

    protected static $namelist = ['Nibbles','Chip','Mickey','Minnie','Pip','Squeaky','Yersinia Pestis','Pixel'];

    public function __construct($name = null) {
        parent::__construct($name);

        $this->get_status()->set(
            Model_Status::MS_STAT_HEALTH, 100,
            Model_Status::MS_STAT_ENERGY, 100,
            Model_Status::MS_STAT_HUNGER, 100,
            Model_Status::MS_STAT_THIRST, 100,
            Model_Status::MS_STAT_SLEEPY, 100
        );

        $this->get_status()->scaling_add(Model_Status::MS_STAT_HUNGER, Model_Status::MS_EFFECT_ITEM, 'npc_animal_mouse', 100);
        $this->get_status()->scaling_add(Model_Status::MS_STAT_THIRST, Model_Status::MS_EFFECT_ITEM, 'npc_animal_mouse', 100);
        $this->get_status()->scaling_add(Model_Status::MS_STAT_ZOMBIFY, Model_Status::MS_EFFECT_GENERIC, 'npc_animal_mouse', 0);
        $this->get_status()->set_fixed_threshold(Model_Status::MS_CHAR_ITEM_SPAWNRATE, 4);
    }

    protected function generate_zombified_body() {
        return false;
    }

    public function create_combatant() {
        return null;
    }

    public function entity_species() {
        return 'Maus';
    }

    public function entity_description() {
        return 'Mäuse können zwar nicht gegen Zombies kämpfen, dafür helfen sie dir aber beim Finden von Gegenständen und Vertilgen von Nahrungsmitteln.';
    }

    public function is_fighter() {
        return false;
    }

    public function icon() {
        return 'mouse.gif';
    }
}