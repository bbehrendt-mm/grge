<?php

abstract class Model_NPC_Animal extends Model_NPC_Nano
{
    protected static $entity_type = Interface_Plentity::IC_NPC_ANIMAL;
    protected $last_hideout = null;

    protected static $movement_scaling = 1;
    protected static $alcohol_scaling = 1;
    protected static $inventory_size = 100;
    protected static $comfort_threshold = 50;

    protected static $namelist = [];

    public function __construct($name = null) {
        if (static::$namelist && $name === null) {
            $list = array();
            for ($i = 0; $i < count(static::$namelist); $i++)
                if (Globals::CurrentGame()->ndp_check(get_called_class(), $i))
                    $list[] = $i;

            if (!$list) {
                Globals::CurrentGame()->ndp_purge(get_called_class());
                $type = mt_rand(0, count(static::$namelist) - 1);
            } else $type = $list[mt_rand(0, count($list) - 1)];

            $name = static::$namelist[$type];
            Globals::CurrentGame()->ndp_register(get_called_class(), $type);
        }

        if (!$name) $name = $this->entity_species();

        parent::__construct($name);

        $this->get_status()->set(
            Model_Status::MS_STAT_HEALTH, 100,
            Model_Status::MS_STAT_ENERGY, 100,
            Model_Status::MS_STAT_HUNGER, 100,
            Model_Status::MS_STAT_THIRST, 100,
            Model_Status::MS_STAT_SLEEPY, 100
        );

        $this->get_status()->scaling_add(Model_Status::MS_STAT_ENERGY, Model_Status::MS_EFFECT_MOVEMENT, 'npc_animal', static::$movement_scaling);
        $this->get_status()->scaling_add(Model_Status::MS_STAT_THIRST, Model_Status::MS_EFFECT_MOVEMENT, 'npc_animal', static::$movement_scaling);
        $this->get_status()->scaling_add(Model_Status::MS_STAT_DRUNK, Model_Status::MS_EFFECT_ITEM, 'npc_animal', static::$alcohol_scaling);

        $this->inventory()->limit(static::$inventory_size);

        new Model_Buffs_Metabolism($this);
        new Model_Buffs_Alcohol($this);
        new Model_Buffs_Zombify($this);
        new Model_Buffs_Nuclear($this);
        new Model_Buffs_Heartbeat($this, -1);
        new Model_Buffs_Backpack($this);
        new Model_Buffs_Transport($this);
        new Model_Buffs_Daytime($this);
        new Model_Buffs_Freeze($this);

        $this->companion(true);
    }

    protected function is_drunk() {
        return $this->status->get(Model_Status::MS_STAT_DRUNK) >= max(5,(100 - static::$comfort_threshold));
    }

    /**
     * @return Model_Items_Abstract_Item|null
     */
    protected function generate_dead_body() {
        return new Model_Items_Body3(true);
    }

    public function ai() {
        $busy = $this->get_status()->retrieve('passout') || $this->get_status()->retrieve('fragile');

        // Item Consumption
        if (!$busy)
            foreach ([Model_Status::MS_STAT_HUNGER, Model_Status::MS_STAT_THIRST, Model_Status::MS_STAT_HEALTH] as $stat)
                if ($this->get_status()->get($stat) <= static::$comfort_threshold || ($this->is_drunk() && Tool_Gambling::random(0.1))) {

                    $ic = Tool_Npc::get_satisfactory_item($this, true, true, $stat ,
                        [Model_Status::MS_STAT_HEALTH => [false, -$this->get_status()->get($stat)/2]],
                        [$stat => [100 - $this->get_status()->get($stat), false], Model_Status::MS_STAT_ZOMBIFY => [0, false]]
                    );

                    if ($ic) {
                        /** @var Model_Items_Abstract_Item $item */
                        list($item, $action) = $ic;
                        Globals::setCurrentPlayer($this);
                        Controller_Act::code_item($item->uin(), $action);
                        Globals::restorePrimaryPlayer();
                    }
                }


        if (($hideout = Tool_Scripts::current_location_hideout()) && $hideout->get_defense() > 0) {
            // At home
            $this->last_hideout = $this->location_class();

            // Go to sleep
            if ($this->get_status()->get(Model_Status::MS_STAT_SLEEPY) < 75 && !$busy)
                new Model_Buffs_Presleep($this->id(), 3, 2);

        } else {
            // Other location

            // Going home
            if (!$busy && $this->last_hideout && $this->location() && !count(Tool_Scripts::at_location($this->location_class(), true, false))) {
                $home_distance = Globals::CurrentGame()->map($this->location_class())->get_distance($this->location_class(), $this->last_hideout);

                if ($home_distance !== false) {
                    $home_distance *= Globals::CurrentGame()->map($this->location_class())->movement_modifier() * $this->get_status()->get(Model_Status::MS_CHAR_DISTANCING);

                    if ($this->get_status()->get(Model_Status::MS_STAT_ENERGY) >= $home_distance && ($this->get_status()->get(Model_Status::MS_STAT_HEALTH) <= static::$comfort_threshold || $this->get_status()->get(Model_Status::MS_STAT_ENERGY) < $home_distance + 10))
                        Controller_Map::code_go(false, $this->last_hideout, true, false, []);
                }
            }
        }
    }

    public function can($type) {
        if ($this->is_drunk() && in_array($type, [Interface_Plentity::IC_TRIGGER_ITEM_FINDINGS, Interface_Plentity::IC_TRIGGER_LOCATION_FINDINGS, Interface_Plentity::IC_TRIGGER_SUPPLIES]))
            return false;
        return parent::can($type);
    }

    public function entity_action() {
        if ($p = parent::entity_action())
            return $p;

        if ($this->is_drunk()) return 'Betrunken';
        return null;
    }

    public function entity_profession() {
        return 'Haustier';
    }

}