<?php

class Model_NPC_Dog extends Model_NPC_Animal
{
    protected static $escort_functions = [Interface_Plentity::IC_ALLOW_ANY];

    protected $last_hideout = null;

    protected static $movement_scaling = 0.5;
    protected static $alcohol_scaling = 5;
    protected static $inventory_size = 20;
    protected static $comfort_threshold = 30;
    
    protected static $abillities = [
        Interface_Plentity::IC_TRIGGER_ITEM_TICKS,
        Interface_Plentity::IC_TRIGGER_ITEM_FINDINGS,
        Interface_Plentity::IC_TRIGGER_LOCATION_FINDINGS,
        Interface_Plentity::IC_TRIGGER_LOCATION_TICKS
    ];

    public function __construct($name) {
        parent::__construct($name);

        $this->get_status()->set(
            Model_Status::MS_STAT_HEALTH, mt_rand(75,90),
            Model_Status::MS_STAT_ENERGY, mt_rand(80,100),
            Model_Status::MS_STAT_HUNGER, mt_rand(50,90),
            Model_Status::MS_STAT_THIRST, mt_rand(55,90),
            Model_Status::MS_STAT_SLEEPY, 100
        );
    }


    /**
     * @return Model_Items_Abstract_Item|null
     */
    protected function generate_dead_body() {
        return new Model_Items_Body3();
    }

    protected function generate_zombified_body() {
        return Model_Combat_Zombies_Ghuldog::factory()->zombiefied_player_id($this->id)->name($this->name())->register_inventory($this->inventory())->strength($this->get_status()->get(Model_Status::MS_STAT_ZOMBIFY)/2, 50, 1);
    }

    public function create_combatant() {
        return Model_Combat_Players_Dog::create_linked_actor($this, 'dogmeat.jpg');
    }
}