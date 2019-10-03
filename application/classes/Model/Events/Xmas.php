<?php
class Model_Events_Xmas extends Model_Events_Event {

    protected static $event_key = 'xmas';
    protected static $event_name = 'Weihnachts-Event';

    public function place_conductor(Model_Places_Abstract_Place $place): void
    {
        $conductor = new Model_NPC_Event_Conductor();
        $conductor->location_class($place->uin());
        Globals::CurrentGameF()->add_npc($conductor);
        $place->log()->add(new Model_Log_Types_Movement(Model_Log_Types_Movement::MOVEMENT_TYPE_ENTER, $conductor->id(), true));
        $this->register_npc_id($conductor->id());
    }

    protected function trigger_activation(): bool
    {
        foreach (Globals::CurrentGameF()->playable_entities() as $pl) if (!Tool_Scripts::is_npc($pl)) {
            new Model_Buffs_Event_Rudolph($pl);
            /** @var $pl Model_Player */
            $pl->log()->add(new Model_Log_Types_Event(static::name(),static::get_key(), true,
                'Ein warmes Licht kriecht über die eiskalte Landschaft.'
            ));
        }

        $this->place_conductor(Globals::CurrentGameF()->location(Globals::CurrentGameF()->map_main()->resolve_fixed_id(1)));
        return true;
    }

    protected function trigger_deactivation(): bool
    {
        $b = parent::trigger_deactivation();

        foreach (Globals::CurrentGameF()->playable_entities() as $pl) {
            $pl->get_status()->set(Model_Status::MS_STAT_FREEZE,0);
            $pl->get_status()->remove('rudolph');
            /** @var $pl Model_Player */
            if (!Tool_Scripts::is_npc($pl))
                $pl->log()->add(new Model_Log_Types_Event(static::name(),static::get_key(), false,
                    'Der Glanz der Tannenbäume erlischt.'
                ));
            foreach ($pl->inventory()->get('Interface_Event') as $i)
                $i->grind();
        }

        return $b;
    }

    public function tick(): bool { return true; }

    public function event_playerCreation(Interface_Plentity $entity): void
    {
        if ($entity->can(Interface_Plentity::IC_TRIGGER_ITEM_FINDINGS))
            new Model_Buffs_Event_Rudolph($entity);

    }

    public function event_locationCreation(Model_Places_Abstract_Place $place): void
    {
        if (Tool_System::instance_of($place, Model_Places_Outworld::cls()))
            $this->place_conductor($place);
    }

    public function event_locationTick(Model_Places_Abstract_Place $place): void
    {}

    public function event_generateHIDStack(Model_Items_Abstract_Item $item, Model_Hid $hid): void
    {
        // Coffee
        if (Tool_System::instance_of($item, Model_Items_Coffee2::cls())) {

            $action = &$hid->get_action('Trinken',true);

            if ($action) $effect = &$action->get_effect(0);
            else $effect = null;
            if ($effect)
                $effect->effect(Model_Status::MS_STAT_FREEZE, -30);
        }
    }

    private function mergedHIDCallback($cls, $name, Model_Action $action): void
    {

        if ($name === 'Trinken' && Tool_System::instance_of($cls, Model_Items_Coffee2::cls())) {

            $effect = &$action->get_effect(0);
            if ($effect)
                $effect->effect(Model_Status::MS_STAT_FREEZE, -30);
        }
    }

    public function event_executeHIDAction($cls, $name, Model_Action $action): void
    {$this->mergedHIDCallback($cls,$name,$action);}

    public function event_renderHIDAction($cls, $name, Model_Action $action): void
    {$this->mergedHIDCallback($cls,$name,$action);}

    public function event_findItem(Model_Places_Abstract_Place $place, Model_Items_Abstract_Item $item): void
    {}

    public function event_blueprintCreation($config_name, $config_category): ?Model_Blueprints
    {
        if ($config_name === 'Abstract_Hideout' && $config_category === 'items') {
            return Model_Blueprints::factory()
                ->add_blueprints(
                    Model_Blueprint::factory()
                        ->id('i:xmas1_cookie1')
                        ->requires('ktc3')
                        ->steps(0)
                        ->name('Plätzchen backen')
                        ->message('Ein weihnachtlicher Durft erfüllt dein Versteck, als du kleine Figürchen aus dem Teig presst und diese zu Plätzchen backst.')
                        ->material([Model_Items_Generic_Cookieproto::cls() => 1])
                        ->produces([Model_Items_Cookie::cls() => 5])
                        ->effect(Model_Effect::factory()
                            ->achieve(Model_Achievement::MA_XMAS)
                        )
                )

                ->add_blueprints(
                    Model_Blueprint::factory()
                        ->id('i:xmas1_cookie2')
                        ->requires('ktc3')
                        ->steps(0)
                        ->name('Besondere Plätzchen backen')
                        ->message('Normale Plätzchen sind langweilig, also fügst du ein paar kreative Extra-Zutaten hinzu...')
                        ->material([Model_Items_Generic_Cookieproto::cls() => 1, Model_Items_Powderpack::cls() => 1])
                        ->produces([Model_Items_Cookie2::cls() => 5])
                        ->effect(Model_Effect::factory()
                            ->achieve(Model_Achievement::MA_XMAS)
                        )
                );
        }

        return null;
    }
}