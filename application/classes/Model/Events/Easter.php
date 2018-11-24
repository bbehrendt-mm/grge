<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Events_Easter extends Model_Events_Event {

    protected static $event_key = 'easter';
    protected static $event_name = 'Oster-Event';

    private $corax_id = -1;

    protected function trigger_activation(): bool
    {
        $home = Globals::CurrentGameF()->map_main()->resolve_fixed_id(2);
        if ($home === null) return false;

        $corax = new Model_NPC_Event_Crow();
        $corax->location_class($home);
        Globals::CurrentGameF()->add_npc($corax);
        Globals::CurrentGameF()->locationF($home)->log()->add(new Model_Log_Types_Movement(Model_Log_Types_Movement::MOVEMENT_TYPE_ENTER, $corax->id(), true));
        $this->corax_id = $corax->id();

        foreach (Globals::CurrentGameF()->playable_entities() as $pl) if (!Tool_Scripts::is_npc($pl)) {
            /** @var $pl Model_Player */
            $pl->log()->add(new Model_Log_Types_Event(static::name(),static::get_key(), true,
                'Der Frühling zeigt seine ersten Blüten.'
            ));
        }

        return true;
    }

    protected function trigger_deactivation(): bool
    {
        foreach (Globals::CurrentGameF()->playable_entities() as $pl) {
            /** @var $pl Model_Player */
            if (!Tool_Scripts::is_npc($pl))
                $pl->log()->add(new Model_Log_Types_Event(static::name(),static::get_key(), false,
                    'Tja, das wars wohl für dieses Jahr.'
                ));
        }

        $npc_inst = Globals::CurrentGameF()->get_npc($this->corax_id);
        if ($npc_inst && $npc_inst->get_status()->alive())
            $npc_inst->kill();

        return true;
    }

    public function tick(): bool
    {
        return true;
    }

    public function event_playerCreation(Interface_Plentity $entity): void {}
    public function event_locationCreation(Model_Places_Abstract_Place $place): void {}
    public function event_locationTick(Model_Places_Abstract_Place $place): void {}
    public function event_generateHIDStack(Model_Items_Abstract_Item $item, Model_Hid $hid): void {}
    public function event_executeHIDAction($cls, $name, Model_Action $action): void {}
    public function event_findItem(Model_Places_Abstract_Place $place, Model_Items_Abstract_Item $item): void {}
    public function event_blueprintCreation($config_name, $config_category): ?Model_Blueprints { return null; }
    public function event_renderHIDAction($cls, $name, Model_Action $action): void {}
}