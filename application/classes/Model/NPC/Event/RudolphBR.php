<?php

class Model_NPC_Event_RudolphBR extends Model_NPC_Event_Rudolph
{
    protected static $alcohol_scaling = 0.4;
    protected static $inventory_size = 180;

    protected function auto_drink(): bool
    {
        return true;
    }

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