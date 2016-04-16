<?php defined('SYSPATH') or die('No direct script access.');

interface Interface_Plentity extends Interface_Cloudshard {

    const IC_TRIGGER_LOCATION_TICKS = 1;
    const IC_TRIGGER_ITEM_TICKS = 2;
    const IC_TRIGGER_ITEM_FINDINGS = 3;
    const IC_TRIGGER_LOCATION_FINDINGS = 4;
    const IC_TRIGGER_SUPPLIES = 5;

    const IC_ALLOW_ANY = 0;
    const IC_ALLOW_SHOW_INVENTORY = 1;
    const IC_ALLOW_ITEM_PICKUP = 2;
    const IC_ALLOW_ITEM_DROP = 3;
    const IC_ALLOW_ITEMS_SIDEUSE = 4;
    const IC_ALLOW_ITEMS_USE = 5;
    const IC_ALLOW_MOVE = 6;
    const IC_ALLOW_MANAGE_ACTIVITY = 7;

    const IC_NPC_NONPC = 0;
    const IC_NPC_GENERIC = 1;
    const IC_NPC_ANIMAL = 2;

    /**
     * @return Model_Status
     */
    public function get_status();

    public function name();

    public function entity_species();
    public function entity_profession();
    public function entity_action();
    public function entity_description();

    public function set_id($new);

    public function location_class($lc = null);

    /** @return Model_Places_Abstract_Place */
    public function location();

    /** @return Model_Inventory */
    public function inventory();

    public function kill();

    public function tick();

    public function ai();

    public function can($type);

    public function id();

    public function type();

    public function companion($newval = null);

    public function allow($type = null);
}