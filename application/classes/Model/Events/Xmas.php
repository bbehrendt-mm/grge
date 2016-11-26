<?php
class Model_Events_Xmas extends Model_Events_Event {

    protected static $event_key = 'xmas';
    protected static $event_name = 'Weihnachts-Event';

    private $npc_list = [];
    private $maps = [];
    private $item_list = [];

    public function register_event_map($map) {
        $this->maps[] = $map;
    }

    public function register_event_item($item) {
        $this->item_list[] = $item;
    }

    public function place_conductor(Model_Places_Abstract_Place $place) {
        /** @global Model_Game $game */
        global $game;

        $conductor = new Model_NPC_Event_Conductor();
        $conductor->location_class($place->uin());
        $game->add_npc($conductor);
        $place->log()->add(new Model_Log_Types_Movement(Model_Log_Types_Movement::MOVEMENT_TYPE_ENTER, $conductor->id(), true));
        $this->npc_list[] = $conductor->id();
    }

    protected function trigger_activation()
    {
        /** @global Model_Game $game */
        global $game;

        foreach ($game->playable_entities() as $pl) if (!Tool_Scripts::is_npc($pl)) {
            /** @var $pl Model_Player */
            $pl->log()->add(new Model_Log_Types_Event(static::name(),static::get_key(), true, "Ein warmes Licht kriecht über die eiskalte Landschaft."));
        }

        $this->place_conductor($game->location($game->map_main()->resolve_fixed_id(1)));
        return true;
    }

    protected function trigger_deactivation() {
        /** @global Model_Game $game */
        global $game;

        foreach ($this->item_list as $iuin) {
            /** @var Model_Items_Abstract_Item $i */
            $i = $game->uin()->get($iuin, Model_Items_Abstract_Item::cls());
            if ($i) $i->grind();
        }

        foreach ($game->playable_entities() as $pl) {
            $pl->get_status()->set(Model_Status::MS_STAT_FREEZE,0);
            /** @var $pl Model_Player */
            if (!Tool_Scripts::is_npc($pl))
                $pl->log()->add(new Model_Log_Types_Event(static::name(),static::get_key(), false, "Der Glanz der Tannenbäume erlischt."));
            foreach ($pl->inventory()->get('Interface_Event') as $i)
                $i->grind();
        }

        foreach ($this->npc_list as $npc) {
            $npc_inst = $game->get_npc($npc);
            if ($npc_inst && $npc_inst->get_status()->alive())
                $npc_inst->kill();
        }

        $d_loc = $game->map_main()->get_by_fixed_id(1);
        if ($d_loc)
            foreach ($this->maps as $map_id) {
                $map = $game->map_by_id($map_id);
                if ($map) {
                    foreach ($map->get_locations() as $subloc)
                        foreach (Tool_Scripts::at_location($subloc) as $p) {
                            $game->location($subloc)->leave($p->id(), Tool_Scripts::is_npc($p) ? Interface_Tickable::IT_TYPE_NPC : Interface_Tickable::IT_TYPE_PLAYER);
                            $p->location_class($d_loc->uin());
                            $d_loc->log()->add(new Model_Log_Types_Movement(Model_Log_Types_Movement::MOVEMENT_TYPE_ENTER, $p->id(), Tool_Scripts::is_npc($p)));
                        }
                }
                $game->unregister_map($map_id);
            }

        return true;
    }

    public function tick() {}

    public function event_playerCreation(Interface_Plentity $entity) {}

    public function event_locationCreation(Model_Places_Abstract_Place $place) {
        if (Tool_System::instance_of($place, Model_Places_Outworld::cls()))
            $this->place_conductor($place);
    }

    public function event_locationTick(Model_Places_Abstract_Place $place) {}

    public function event_generateHIDStack(Model_Items_Abstract_Item &$item, Model_Hid &$hid) {
        // Coffee
        if (Tool_System::instance_of($item, Model_Items_Coffee2::cls())) {

            $action = &$hid->get_action('Trinken',true);

            if ($action) $effect = &$action->get_effect(0);
            else $effect = null;
            if ($effect)
                $effect->effect(Model_Status::MS_STAT_FREEZE, -30);
        }
    }

    private function mergedHIDCallback($cls, $name, Model_Action &$action) {

        if (Tool_System::instance_of($cls, Model_Items_Coffee2::cls()) && $name == 'Trinken') {

            $effect = &$action->get_effect(0);
            if ($effect)
                $effect->effect(Model_Status::MS_STAT_FREEZE, -30);
        }
    }

    public function event_executeHIDAction($cls, $name, Model_Action &$action) {$this->$this->mergedHIDCallback($cls,$name,$action);}

    public function event_renderHIDAction($cls, $name, Model_Action &$action) {$this->mergedHIDCallback($cls,$name,$action);}

    public function event_findItem(Model_Places_Abstract_Place $place, Model_Items_Abstract_Item &$item) {}

    public function event_blueprintCreation($config_name, $config_category)
    {
        if ($config_name == 'Abstract_Hideout' && $config_category == 'items') {
            return Model_Blueprints::factory()
                ->add_blueprints(
                    Model_Blueprint::factory()
                        ->id('i:xmas1_cookie1')
                        ->requires('ktc3')
                        ->name('Plätzchen backen')
                        ->message('Ein weihnachtlicher Durft erfüllt dein Versteck, als du kleine Figürchen aus dem Teig presst und diese zu Plätzchen backst.')
                        ->material(['Model_Items_Generic_Cookieproto' => 1])
                        ->produces(['Model_Items_Cookie' => 5])
                        ->effect(Model_Effect::factory()
                            ->achieve(Model_Achievement::MA_XMAS)
                        )
                )

                ->add_blueprints(
                    Model_Blueprint::factory()
                        ->id('i:xmas1_cookie2')
                        ->requires('ktc3')
                        ->name('Besondere Plätzchen backen')
                        ->message('Normale Plätzchen sind langweilig, also fügst du ein paar kreative Extra-Zutaten hinzu...')
                        ->material(['Model_Items_Generic_Cookieproto' => 1, 'Model_Items_Powderpack' => 1])
                        ->produces(['Model_Items_Cookie2' => 5])
                        ->effect(Model_Effect::factory()
                            ->achieve(Model_Achievement::MA_XMAS)
                        )
                );
        }

        return null;
    }
}