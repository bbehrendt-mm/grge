<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Events_Fools extends Model_Events_Event {

    protected static $event_key = 'aprilsfools';
    protected static $event_name = '1. April';

    protected static $show_info = false;

    protected static $dm_begin = [01,04,0];
    protected static $dm_end   = [02,04,0];

    protected static $additional_effects = [ Model_Events_Event::MEE_EFFECT_FOOLS ];

    protected function trigger_activation(): bool {return true;}

    public function tick(): bool { return true; }

    public function event_playerCreation(Interface_Plentity $entity): void {}
    public function event_locationCreation(Model_Places_Abstract_Place $place): void {}
    public function event_locationTick(Model_Places_Abstract_Place $place): void {}
    public function event_generateHIDStack(Model_Items_Abstract_Item $item, Model_Hid $hid): void {}
    public function event_executeHIDAction($cls, $name, Model_Action $action): void {}
    public function event_findItem(Model_Places_Abstract_Place $place, Model_Items_Abstract_Item $item): void {}
    public function event_blueprintCreation($config_name, $config_category): ?Model_Blueprints { return null; }
    public function event_renderHIDAction($cls, $name, Model_Action $action): void {}
}