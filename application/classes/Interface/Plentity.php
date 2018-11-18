<?php defined('SYSPATH') or die('No direct script access.');

interface Interface_Plentity extends Interface_Cloudshard {

    public const IC_TRIGGER_LOCATION_TICKS = 1;
    public const IC_TRIGGER_ITEM_TICKS = 2;
    public const IC_TRIGGER_ITEM_FINDINGS = 3;
    public const IC_TRIGGER_LOCATION_FINDINGS = 4;
    public const IC_TRIGGER_SUPPLIES = 5;

    public const IC_ALLOW_ANY = 0;
    public const IC_ALLOW_SHOW_INVENTORY = 1;
    public const IC_ALLOW_ITEM_PICKUP = 2;
    public const IC_ALLOW_ITEM_DROP = 3;
    public const IC_ALLOW_ITEMS_SIDEUSE = 4;
    public const IC_ALLOW_ITEMS_USE = 5;
    public const IC_ALLOW_MOVE = 6;
    public const IC_ALLOW_MANAGE_ACTIVITY = 7;

    public const IC_NPC_NONPC = 0;
    public const IC_NPC_GENERIC = 1;
    public const IC_NPC_ANIMAL = 2;
    public const IC_NPC_HUMANOID = 3;

    /**
     * @return Model_Status
     */
    public function get_status(): Model_Status;

    public function name(): string;

    public function icon();

    public function entity_species();
    public function entity_profession();
    public function entity_action();
    public function entity_description();

    public function set_id($new);

    public function location_class($lc = null): int;

    /**
     * @return Model_Places_Abstract_Place
     */
    public function location(): Model_Places_Abstract_Place;

    /**
     * @return Model_Inventory
     */
    public function inventory(): Model_Inventory;

    public function kill();

    public function tick();

    public function ai();

    public function can($type);

    public function id();

    public function type();

    public function companion($newval = null): bool;

    public function allow($type = null);

    public function item_preaction(Model_Items_Abstract_Item $item,$action);
    public function item_reaction();

    /**
     * @return Model_Hid
     */
    public function hid(): Model_Hid;

    public function set_escape_target($e = null);
    public function get_escape_target();

    public function enable_escape();
    public function disable_escape();
    public function can_escape();
}