<?php

class Model_NPC_Dog extends Model_NPC_Nano
{
    protected static $entity_type = Interface_Plentity::IC_NPC_ANIMAL;

    public function __construct($name) {
        parent::__construct($name);

        $this->get_status()->set(
            Model_Status::MS_STAT_HEALTH, mt_rand(75,90),
            Model_Status::MS_STAT_ENERGY, mt_rand(80,100),
            Model_Status::MS_STAT_HUNGER, mt_rand(50,90),
            Model_Status::MS_STAT_THIRST, mt_rand(55,90),
            Model_Status::MS_STAT_SLEEPY, 100
        );

        $this->get_status()->scaling_add(Model_Status::MS_STAT_ENERGY, Model_Status::MS_EFFECT_MOVEMENT, 'npc_animal', 0.5);
        $this->get_status()->scaling_add(Model_Status::MS_STAT_THIRST, Model_Status::MS_EFFECT_MOVEMENT, 'npc_animal', 0.5);
        $this->get_status()->scaling_add(Model_Status::MS_STAT_DRUNK, Model_Status::MS_EFFECT_ITEM, 'npc_animal', 5);

        $this->inventory()->limit(20);

        new Model_Buffs_Metabolism($this);
        new Model_Buffs_Alcohol($this);
        new Model_Buffs_Zombify($this);
        new Model_Buffs_Nuclear($this);
        new Model_Buffs_Heartbeat($this, -1);
        new Model_Buffs_Backpack($this);
        new Model_Buffs_Transport($this);
        new Model_Buffs_Daytime($this);
        new Model_Buffs_Freeze($this);
    }

    /**
     * @return Model_Items_Abstract_Item|null
     */
    protected function generate_dead_body() {
        return new Model_Items_Body3();
    }

    protected function generate_zombified_body() {
        return null;
    }

    public function ai() {

    }

    public function can($type) {
        return true;
    }

    public function companion($newval = null) {
        return true;
    }
}