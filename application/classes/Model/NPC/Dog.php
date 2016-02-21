<?php

class Model_NPC_Dog extends Model_NPC_Nano
{
    protected static $entity_type = Interface_Plentity::IC_NPC_ANIMAL;
    protected static $escort_functions = [Interface_Plentity::IC_ALLOW_ANY];

    protected $last_hideout = null;

    protected static $abillities = [
        Interface_Plentity::IC_TRIGGER_ITEM_TICKS,
        Interface_Plentity::IC_TRIGGER_ITEM_FINDINGS,
        Interface_Plentity::IC_TRIGGER_LOCATION_FINDINGS,
        Interface_Plentity::IC_TRIGGER_LOCATION_TICKS
    ];

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

        $this->companion(true);
    }

    /**
     * @return Model_Items_Abstract_Item|null
     */
    protected function generate_dead_body() {
        return new Model_Items_Body3();
    }

    protected function generate_zombified_body() {
        return Model_Combat_Zombies_Ghuldog::factory()->zombiefied_player_id($this->id)->name($this->name())->register_inventory($this->inventory())->strength($this->get_status()->get(Model_Status::MS_STAT_ZOMBIFY)/2, 50, 1);
    }

    public function create_combatant() {
        return Model_Combat_Players_Dog::create_linked_actor($this);
    }

    public function ai() {
        /** @global Model_Game $game */
        global $game;

        $busy = $this->get_status()->retrieve('passout') || $this->get_status()->retrieve('fragile');

        // Item Consumption
        if (!$busy)
            foreach ([Model_Status::MS_STAT_HUNGER, Model_Status::MS_STAT_THIRST, Model_Status::MS_STAT_HEALTH] as $stat)
                if ($this->get_status()->get($stat) <= 30) {

                    $ic = Tool_Npc::get_satisfactory_item($this, true, true, $stat ,
                        [Model_Status::MS_STAT_HEALTH => [false, -$this->get_status()->get($stat)/2]],
                        [$stat => [100 - $this->get_status()->get($stat), false], Model_Status::MS_STAT_ZOMBIFY => [0, false]]
                    );

                    if ($ic) {
                        /** @var Model_Items_Abstract_Item $item */
                        list($item, $action) = $ic;
                        Controller_Game::delegate($this, function() use ($item, $action) {
                            Controller_Act::code_item($item->uin(), $action);
                        });
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
            if (!$busy && $this->last_hideout && !count(Tool_Scripts::at_location($this->location_class(), true, false))) {
                $home_distance = $game->map($this->location_class())->get_distance($this->location_class(), $this->last_hideout);

                if ($home_distance !== false) {
                    $home_distance *= $game->map($this->location_class())->movement_modifier() * $this->get_status()->get(Model_Status::MS_CHAR_DISTANCING);

                    if ($this->get_status()->get(Model_Status::MS_STAT_ENERGY) >= $home_distance && ($this->get_status()->get(Model_Status::MS_STAT_HEALTH) <= 30 || $this->get_status()->get(Model_Status::MS_STAT_ENERGY) < $home_distance + 10))
                        Controller_Map::code_go(false, $this->last_hideout, true, false, []);
                }
            }
        }
    }
}