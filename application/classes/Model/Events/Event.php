<?php defined('SYSPATH') OR die('No direct access allowed.');

abstract class Model_Events_Event {

    protected static $event_key = '';
    protected $active = false;
    protected static $event_name = '';

    public function __construct() {
        $this->trigger();
    }

    public static function name(): string {
        return static::$event_name;
    }

    protected static function get_game_time() {
        return Globals::hasCurrentGame() ? Globals::CurrentGameF()->next_tick() : time();
    }

    public static function is_current(): bool {
        return (static::$event_key ? (Tool_Events::current(static::get_game_time()) === static::$event_key) : false);
    }

    public static function get_key(): string {
        return static::$event_key;
    }

    public function is_active(): bool {
        return $this->active;
    }

    public function trigger(): void {
        if (static::is_current() && !$this->is_active()) {
            $this->active = $this->trigger_activation();
            if ($this->active) Globals::CurrentGameF()->set_event_index($this);
        }

        elseif (!static::is_current() && $this->is_active()) {
            $this->active = !$this->trigger_deactivation();
            if (!$this->active) Globals::CurrentGameF()->unset_event_index($this);
        }
    }

    abstract protected function trigger_activation(): bool;
    abstract protected function trigger_deactivation(): bool;
    abstract public function tick(): bool;
    abstract public function event_playerCreation(Interface_Plentity $entity): void;
    abstract public function event_locationCreation(Model_Places_Abstract_Place $place): void;
    abstract public function event_locationTick(Model_Places_Abstract_Place $place): void;
    abstract public function event_generateHIDStack(Model_Items_Abstract_Item $item, Model_Hid $hid): void;
    abstract public function event_executeHIDAction($cls, $name, Model_Action $action): void;
    abstract public function event_renderHIDAction($cls, $name, Model_Action $action): void;
    abstract public function event_findItem(Model_Places_Abstract_Place $place, Model_Items_Abstract_Item $item): void;
    abstract public function event_blueprintCreation($config_name, $config_category): ?Model_Blueprints;

}