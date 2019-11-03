<?php defined('SYSPATH') OR die('No direct access allowed.');

abstract class Model_Events_Event {

    public const MEE_EFFECT_UNLOCK = 1;
    public const MEE_EFFECT_FOOLS  = 2;
    public const MEE_EFFECT_TICKET = 3;

    protected static $event_name = '';
    protected static $event_key = '';
    protected static $show_info = true;

    protected static $additional_effects = [];

    // dm_begin and dm_end are arrays in the form of [day, month, relative year].
    // dm_begin is the first day of the event, dm_end is the day AFTER the end of the event
    protected static $dm_begin = null;  // Example [ 5, 3,0] for Mar05 or [10,12,0] for Dec12
    protected static $dm_end   = null;  // Example [19, 3,0] for Mar19 or [ 2, 1,1] for Jan02, next year
    protected static $dm_days  = null;

    protected $active = false;

    private $npc_list = [];
    private $item_list = [];
    private $map_list = [];

    public function __construct() {
        $this->trigger();
    }

    public static function additional_effect(int $e): bool {
        return in_array( $e, static::$additional_effects );
    }

    protected static function get_start(int $y = 0): ?DateTime {
        if (static::$dm_begin !== null)
            return new DateTime( ((int)((new DateTime())->format('Y'))+$y+static::$dm_begin[2]) . '-' . static::$dm_begin[1] . '-' . static::$dm_begin[0]);
        elseif ( static::$dm_days !== null && $end = static::get_end($y) )
            return $end->sub( new DateInterval('P' . static::$dm_days . 'D') );
        else return null;
    }

    protected static function get_end(int $y = 0): ?DateTime {
        if (static::$dm_end !== null)
            return new DateTime( ((int)((new DateTime())->format('Y'))+$y+static::$dm_end[2]) . '-' . static::$dm_end[1] . '-' . static::$dm_end[0]);
        elseif ( static::$dm_days !== null && $end = static::get_start($y) )
            return $end->add( new DateInterval('P' . static::$dm_days . 'D') );
        else return null;
    }

    public static function get_season(?DateTime &$begin, ?DateTime &$end, $offset = null): bool {
        $now = new DateTime();
        if ($auto_adv = ($offset === null)) $offset = 0;

        if (($tmp_end = static::get_end(0+$offset)) === null) return false;
        if ($auto_adv && $tmp_end < $now) {
            $tmp_start = static::get_start(1+$offset);
            $tmp_end   = static::get_end(1+$offset);
        } else $tmp_start = static::get_start(0+$offset);

        if ($tmp_start !== null && $tmp_end !== null) {
            $begin = $tmp_start;
            $end = $tmp_end;
            return true;
        } else return false;
    }

    public static function check_season(DateTime $d, ?DateTime &$a = null, ?DateTime &$b = null): bool {
        $i = 0; $j = 0;
        while ( static::get_season($a, $b, $i) && abs($i) < 5 ) {
            if      ($a <= $d && $b > $d) return true;
            elseif ($j === 0) {
                if ($a > $d) $j = -1;
                if ($b < $d) $j =  1;
                $i += $j;
            } else {
                if ( ($j < 0 && $a < $d) || ($j > 0 && $b > $d)) return false;
                $i += $d;
            }
        }
        return false;
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

    public static function visible(): bool {
        return static::$show_info;
    }

    protected static function get_game_time() {
        return Globals::hasCurrentGame() ? Globals::CurrentGameF()->next_tick() : time();
    }

    public static function is_current(): bool {
        foreach (Tool_Events::current_events(static::get_game_time()) as $event)
            if (Tool_System::instance_of( get_called_class(), $event ))
                return true;
        return false;
    }

    public static function get_key(): ?string {
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