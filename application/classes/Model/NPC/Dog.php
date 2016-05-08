<?php

class Model_NPC_Dog extends Model_NPC_Animal
{
    protected static $escort_functions = [Interface_Plentity::IC_ALLOW_ANY];

    protected $last_hideout = null;

    protected static $movement_scaling = 0.7;
    protected static $alcohol_scaling = 6;
    protected static $inventory_size = 18;
    protected static $comfort_threshold = 40;
    
    protected static $abillities = [
        Interface_Plentity::IC_TRIGGER_ITEM_TICKS,
        Interface_Plentity::IC_TRIGGER_ITEM_FINDINGS,
        Interface_Plentity::IC_TRIGGER_LOCATION_FINDINGS,
        Interface_Plentity::IC_TRIGGER_LOCATION_TICKS,
    ];

    protected static $namelist = ['Fifi','Brutus','Collie','Sparky','George','Puppy','Sir Doggington','Hank'];
    
    public function __construct($name = null) {
        parent::__construct($name);

        $this->get_status()->set(
            Model_Status::MS_STAT_HEALTH, mt_rand(55,70),
            Model_Status::MS_STAT_ENERGY, mt_rand(70,90),
            Model_Status::MS_STAT_HUNGER, mt_rand(40,70),
            Model_Status::MS_STAT_THIRST, mt_rand(45,70),
            Model_Status::MS_STAT_SLEEPY, 85
        );
        
        $this->inventory()->add(new Model_Items_Leash());
    }

    
    protected function is_leashed() {
        /** @var Model_Items_Leash $leash */
        $leash = $this->inventory()->get(Model_Items_Leash::cls());
        if ($leash) $leash = $leash[0];
        
        return $leash ? $leash->is_active() : false;
    }

    public function allow($type = null) {
        if ($type == Interface_Plentity::IC_ALLOW_MOVE && $this->is_leashed())
            return false;
        else return parent::allow($type);
    }

    public function ai() {
        if ($this->is_leashed()) return;
        else parent::ai();
    }

    protected function generate_zombified_body() {
        return Model_Combat_Zombies_Ghuldog::factory()->zombiefied_player_id($this->id)->name($this->name())->register_inventory($this->inventory())->strength($this->get_status()->get(Model_Status::MS_STAT_ZOMBIFY)/2, 50, 1);
    }

    public function create_combatant() {
        return Model_Combat_Players_Dog::create_linked_actor($this, 'dog.jpg');
    }

    public function entity_species() {
        return 'Hund';
    }

    public function entity_description() {
        return 'Der beste Freund des Menschen ist auch in der Zombie-Apokalypse ein nützlicher Begleiter. Hunde transportieren Gegenstände und helfen dir im Kampf.';
    }

    public function is_fighter() {
        return !$this->is_leashed() && parent::is_fighter();
    }
}