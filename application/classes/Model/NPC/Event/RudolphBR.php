<?php

class Model_NPC_Event_RudolphBR extends Model_NPC_Event_Rudolph
{
    const RDLPH_EVENT_STAT_UNDEFINED = 0;
    const RDLPH_EVENT_STAT_SOBER = 1;
    const RDLPH_EVENT_STAT_TIPSY = 2;
    const RDLPH_EVENT_STAT_DRUNK = 3;

    protected static $escort_functions = [Interface_Plentity::IC_ALLOW_ANY];

    protected $last_hideout = null;

    protected static $movement_scaling = 1;
    protected static $alcohol_scaling = 0.4;
    protected static $inventory_size = 180;
    protected static $comfort_threshold = 55;

    protected $am_stat_before = Model_NPC_Event_Rudolph::RDLPH_EVENT_STAT_UNDEFINED;
    protected $am_stat_latest = Model_NPC_Event_Rudolph::RDLPH_EVENT_STAT_UNDEFINED;
    protected $am_strong = false;
    protected $am_auto = false;
    protected $am_item = true;

    protected $light = false;
    
    protected static $abillities = [
        Interface_Plentity::IC_TRIGGER_ITEM_TICKS,
        Interface_Plentity::IC_TRIGGER_LOCATION_TICKS,
        Interface_Plentity::IC_TRIGGER_ITEM_FINDINGS
    ];


    protected function generate_zombified_body() {
        return null;
    }

    public function is_fighter() {
        return true;
    }

    public function create_combatant() {
        return Model_Combat_Players_Rudolph::create_linked_actor($this, $this->is_drunk() ? 'rudolph_d.jpg' : 'rudolph.jpg');
    }

    public function entity_description() {
        return 'Dieses majestätische Tier ist mit einer leuchtenden roten Nase ausgestattet, die Feinden ordentlich einheizen kann...';
    }
}