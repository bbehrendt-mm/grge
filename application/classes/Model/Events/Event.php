<?php defined('SYSPATH') OR die('No direct access allowed.');

abstract class Model_Events_Event {

    protected static $event_key = '';
    protected $active = false;
    protected static $event_name = '';

    private $npc_list = [];
    private $item_list = [];
    private $map_list = [];

    public function __construct() {
        $this->trigger();
    }

    public function register_event_map_id($map): void {
        $this->map_list[] = $map;
    }

    public function register_npc_id($npc): void {
        $this->npc_list[] = $npc;
    }

    public function register_item_id($iid): void {
        $this->item_list[] = $iid;
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
    protected function trigger_deactivation(): bool
    {
        foreach ($this->item_list as $iuin) {
            /** @var Model_Items_Abstract_Item $i */
            $i = Globals::CurrentGameF()->uin()->get($iuin, Model_Items_Abstract_Item::cls());
            if ($i) $i->grind();
        }

        foreach ($this->npc_list as $npc) {
            $npc_inst = Globals::CurrentGameF()->get_npc($npc);
            if ($npc_inst && $npc_inst->get_status()->alive())
                $npc_inst->kill();
        }

        $d_loc = Globals::CurrentGameF()->map_main()->get_by_fixed_id(1);
        if ($d_loc)
            foreach ($this->map_list as $map_id) {
                $map = Globals::CurrentGameF()->map_by_id($map_id);
                if ($map) {
                    foreach ($map->get_locations() as $subloc)
                        foreach (Tool_Scripts::at_location($subloc) as $p) {
                            Globals::CurrentGameF()->locationF($subloc)->leave($p->id(), Tool_Scripts::is_npc($p) ? Interface_Tickable::IT_TYPE_NPC : Interface_Tickable::IT_TYPE_PLAYER);
                            $p->location_class($d_loc->uin());
                            $d_loc->log()->add(new Model_Log_Types_Movement(Model_Log_Types_Movement::MOVEMENT_TYPE_ENTER, $p->id(), Tool_Scripts::is_npc($p)));
                        }
                }
                Globals::CurrentGameF()->unregister_map($map_id);
            }

        return true;
    }

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