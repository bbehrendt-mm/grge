<?php

class Model_NPC_Nano extends Model_Cloudshard implements Interface_Plentity
{

    protected $name;
    protected $status;
    protected $location;
    protected $inventory;
    protected $livetime = 0;
    protected $id = null;
    protected $escort = false;

    protected static $entity_type = Interface_Plentity::IC_NPC_GENERIC;
    protected static $escort_functions = [];
    protected static $abillities = [];

    protected static $death_is_enemy = false;

    protected static $handle_death = true;

    public function __construct($name) {
        $this->name = $name;

        // Init sub-objects
        $this->status = new Model_Status();
        $this->inventory = new Model_Inventory(null, true);
    }

    /**
     * @return Model_Status
     */
    public function get_status() {
        return $this->status;
    }

    /**
     * @return string
     */
    public function name() {
        return $this->name;
    }

    /**
     * Returns player location id or changes it
     * @param int $newval Set if you want to change locations; the return value will be the new location
     * @return int
     */
    public function location_class($newval = NULL) {
        if ($newval !== NULL)
            $this->location = $newval;

        return $this->location;
    }

    /**
     * Returns player location object
     * @return Model_Places_Abstract_Place
     */
    final public function location() {
        /**
         * @global $game Model_Game
         */
        global $game;

        if (!$game->location($this->location))
            $this->location_class($game->map_main()->resolve_fixed_id(1));

        return $game->location($this->location);
    }

    /**
     * @return Model_Inventory
     */
    public function inventory() {
        return $this->inventory;
    }

    /**
     * @return Model_Items_Abstract_Item|null
     */
    protected function generate_dead_body() {
        return null;
    }

    protected function generate_zombified_body() {
        return null;
    }

    public function create_combatant() {
        return null;
    }

    protected function handle_death() {
        return static::$handle_death;
    }

    public function kill() {
        /** @global Model_Game $game */
        global $game;

        $this->get_status()->alive(false);

        if ($this->handle_death()) {
            $drop_inv = new Model_Inventory();
            foreach ($this->inventory->get() as $item)
                if ($dropping = $item->drop_dead()) {

                    if (is_array($dropping)) foreach ($dropping as $d_drop) $drop_inv->add($d_drop);
                    else $drop_inv->add($dropping);
                }

            $body = $this->generate_dead_body();
            if ($body) $drop_inv->add($body);

            $this->inventory = $drop_inv;

            if ($this->location()) {
                if ($this->get_status()->get(Model_Status::MS_STAT_ZOMBIFY) >= 50 && ($ghul = $this->generate_zombified_body())) {
                    $this->location()->log()->add(new Model_Log_Types_Item(Model_Log_Types_Item::MLTI_ZOMBIFY, [], $this->id()));
                    $game->register_ghul($this->location_class(), $ghul);
                } else {
                    foreach ($this->inventory()->get() as $d)
                        $this->location()->inventory()->add($d);

                    $this->location()->log()->add(new Model_Log_Types_Item(static::$death_is_enemy ? Model_Log_Types_Item::MLTI_DEATH_ENEMY : Model_Log_Types_Item::MLTI_DEATH, $this->inventory()->get(), $this->id()));
                }

                if (count(Tool_Scripts::at_location($this->location_class(), true, true)) == 0) $this->location()->vacate();
            }
        }
    }

    public function tick()
    {
        /**
         * @var $buff Model_Buffs_Abstract_Buff
         */
        if (!$this->get_status()->alive()) return;

        $this->livetime++;

        $this->get_status()->set_cause_of_death("Multiorganversagen");
        $this->get_status()->tick();
        $this->get_status()->clear_cause_of_death();
    }

    public function ai() {/** NANO NPC must NOT implement any AI! */}

    public function can($type) {
        return in_array($type, static::$abillities);
    }

    public function id() {
        return $this->id;
    }

    public function type() {
        return static::$entity_type;
    }

    /**
     * Returns the companion state, or sets it when newval is given
     * @param null $newval
     * @return bool
     */
    public function companion($newval = null) {
        if ($newval === null) return $this->escort;
        else return $this->escort = $newval;
    }

    public function set_id($new) {
        if ($this->id !== null && $this->id != $new)
            throw new Exception('Attempt to rebind PE ID!');
        $this->id = $new;
    }

    public function allow($type = null) {
        if ($type === null) return $this->escort ? static::$escort_functions : [];
        return $this->escort ? (in_array($type, static::$escort_functions) || in_array(Interface_Plentity::IC_ALLOW_ANY, static::$escort_functions)) : false;
    }

    public function entity_species() {
        return '';
    }

    public function entity_profession() {
        return '';
    }

    public function entity_action() {
        if ($buff = $this->get_status()->retrieve('fragile'))
            return $buff->name();
        else return null;
    }

    public function entity_description() {
        return '';
    }
    
    public function is_fighter() {
        return true;
    }

    /**
     * @return Model_Hid|null
     */
    public function hid() {
        return Model_Hid::factory();
    }
}