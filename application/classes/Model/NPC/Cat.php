<?php

class Model_NPC_Cat extends Model_NPC_Animal
{
    protected static $escort_functions = [
        Interface_Plentity::IC_ALLOW_SHOW_INVENTORY, Interface_Plentity::IC_ALLOW_ITEM_DROP,
        Interface_Plentity::IC_ALLOW_ITEMS_SIDEUSE, Interface_Plentity::IC_ALLOW_ITEMS_USE,
        Interface_Plentity::IC_ALLOW_MOVE, Interface_Plentity::IC_ALLOW_MANAGE_ACTIVITY
    ];

    protected $last_hideout = null;

    protected static $movement_scaling = 0.7;
    protected static $alcohol_scaling = 10;
    protected static $inventory_size = 5;
    protected static $comfort_threshold = 70;
    
    protected static $abillities = [
        Interface_Plentity::IC_TRIGGER_ITEM_TICKS,
        Interface_Plentity::IC_TRIGGER_LOCATION_TICKS
    ];

    protected static $namelist = ['Muffin','Grumpy','Cathy','Misha','Catface Scratchalot','Minka','Scarlet','Felidae'];
    
    public function __construct($name = null) {
        parent::__construct($name);

        $this->get_status()->set(
            Model_Status::MS_STAT_HEALTH, mt_rand(50,80),
            Model_Status::MS_STAT_ENERGY, mt_rand(80,90),
            Model_Status::MS_STAT_HUNGER, mt_rand(80,90),
            Model_Status::MS_STAT_THIRST, mt_rand(80,90),
            Model_Status::MS_STAT_SLEEPY, 85
        );
    }

    /**
     * @return Model_Items_Abstract_Item|null
     */
    protected function generate_dead_body() {
        return new Model_Items_Body3();
    }

    protected function generate_zombified_body() {
        return Model_Combat_Zombies_Ghuldog::factory()->zombiefied_player_id($this->id)->name($this->name())->register_inventory($this->inventory())->strength($this->get_status()->get(Model_Status::MS_STAT_ZOMBIFY)/4, 25, 1);
    }

    public function create_combatant() {
        return Model_Combat_Players_Cat::create_linked_actor($this, 'cat.jpg');
    }

    public function entity_species() {
        return 'Katze';
    }

    public function entity_description() {
        return 'Eine niedliche Katze, die im Kampf auch mal die Krallen zeigt.';
    }
}