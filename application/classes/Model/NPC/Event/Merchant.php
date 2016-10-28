<?php

class Model_NPC_Event_Merchant extends Model_NPC_Humanoid
{
    protected static $buffs_heartbeat_name = 'Model_Buffs_Event_Fakeheart';
    protected static $buffs_metabolism_name = 'Model_Buffs_Event_Fakemetabolism';
    protected static $buffs_place_various = false;

    protected static $abillities = [];

    protected static $handle_death = false;

    public function __construct($name = null) {
        parent::__construct('Vermodernder Händler');
    }

    public function entity_action() {
        return 'Hat die billigsten Preise';
    }

    public function entity_description() {
        return 'Guten Abend, werter Kunde! Haben Sie Interesse, GEHIIIIIIIIIIRNE zu erwerben? GEHIIIIRNE sind eine wundervolle Geldanlage und außerdem noch sehr nützlich im täglichen Leben! Nur hier bekommen Sie GEHIIIIIIIRNE zum absoluten Hammerpreis!';
    }

    public function is_fighter() {
        return false;
    }

    public function kill() {
        if ($this->location()) $this->location()->log()->add(new Model_Log_Types_Movement(Model_Log_Types_Movement::MOVEMENT_TYPE_LEAVE, $this->id(), true));
        parent::kill();
    }
}