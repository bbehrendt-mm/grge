<?php /** @noinspection ALL */

class Model_Blueprint {

    public const BP_MOD_ENERGY = 1;

    /**
     * @var Model_Effect $effect
     */
    private $effect;

    private $iid;
    private $is_room = false;

    private $room_clear = false;
    private $room_clear_sat = true;

    private $space_requirement = 0;

    private $obj_name;
    private $obj_description;
    private $obj_copy_usage = true;

    /** @var Struct_ItemMaterial[] $items  */
    private $items = [];

    /** @var Struct_ItemEntry[] $produces  */
    private $produces = [];
    private $produces_advanced = [];
    private $emplaces = [];
    private $emplaces_action_data = [];

    private $requires = [];
    private $requires_local = [];
    private $requires_tags = [];
    private $provides = [];
    private $provides_room = [];
    private $removes = [];
    private $room_requirements = [];
    private $use_global_blocking = false;

    private $remove_tags = [];
    private $add_tags = [];

    private $energy = 0;
    private $decay = 0;
    private $decay_speed = 0;
    private $deco_value = 0;
    private $steps = 1;
    private $build_condition;
    private $opt_show_condition;
    private $user_message;
    private $defense = 0;
    private $categories = [];
    private $modifiers = [];
    private $confirmation = false;

    /** @var bool|array|callable|int */
    private $zombie_kills = false;
    private $zombies_optional = false;

    /**
     * Creates a new blueprint instance
     * @return Model_Blueprint
     */
    public static function factory(): Model_Blueprint {
        return new self();
    }

    public function get_item_decider() : callable {
        $cache = $this->items;
        return function(Model_Items_Abstract_Item $item) use ($cache) {
            foreach ($cache as $entry)
                if (get_class($item) === $entry->class)
                    return $entry->get_decider()($item);
            return true;
        };
    }

    /**
     * Adds an object modifier to this blueprint
     * @param int $mod Mod type;
     * @param callable $f Modifier function; receives the player and precondition data as parameters; it may also receive additional parameters depending on the mod type
     * @return Model_Blueprint
     */
    public function add_modifier($mod, $f): Model_Blueprint {
        switch ($mod) {
            case static::BP_MOD_ENERGY:
                $this->modifiers[] = function($player, $pre, $room) use ($f) {
                    /** @var Model_Blueprint $bp */
                    $this->energy = $f($player, $pre, $this->energy, $room);
                };
        }
        return $this;
    }

    public function add_modifier_builder($daytime_bonus = 0.25, $handyman_bonus = 0.1): void
    {
        $this->add_modifier(self::BP_MOD_ENERGY, function($pl,$pre,$e) use ($daytime_bonus,$handyman_bonus) { /** @var Model_Player $pl */
            $mod = 1;
            if ($daytime_bonus  !== false && Tool_Scripts::get_timeofday($pl) === 'morning')  $mod -= $daytime_bonus;   // Daytime bonus
            if ($handyman_bonus !== false && $pl->get_status()->retrieve('tr_handyman')) $mod -= $handyman_bonus;  // Handyman Bonus
            return max(min(1,$e),floor($e*$mod));
        });
    }

    /**
     * Applies all modifiers, and clears the modifier cache
     *
     * @param Model_Player $player
     * @param string[]     $pre
     * @param              $room
     *
     * @return Model_Blueprint
     */
    public function modify($player, $pre, $room): \Model_Blueprint
    {
        foreach ($this->modifiers as $mod)
            $mod($player, $pre, $room);
        $this->modifiers = [];
        return $this;
    }

    /**
     * Sets the space requirement
     * @param number|null $set
     * @return Model_Blueprint|int
     */
    public function space($set = null) {
        if ($set === null) return $this->space_requirement;
        else {
            $this->space_requirement = $set;
            return $this;
        }
    }

    /**
     * Setter / Getter for the blueprint name
     * @param null|string $name New name
     * @return Model_Blueprint|string
     */
    public function name($name = null) {
        if ($name === null)
            return $this->obj_name;
        else {
            $this->obj_name = $name;
            return $this;
        }
    }

    /**
     * Setter / Getter for the blueprint confirmation message
     * @param null|string|false $msg Confirmation message or false to disable confirmation.
     * @return Model_Blueprint|string|bool
     */
    public function confirm($msg = null) {
        if ($msg === null)
            return $this->confirmation;
        else {
            $this->confirmation = $msg;
            return $this;
        }
    }

    /**
     * @param bool $b
     *
     * @return Model_Blueprint|bool
     */
    public function global_blocking(?bool $b = null) {
        if ($b === null)
            return $this->use_global_blocking;
        else {
            $this->use_global_blocking = $b;
            return $this;
        }
    }

    /**
     * Setter for the blueprint categories
     * @param array|string $name New category name
     * @return Model_Blueprint
     */
    public function category($name): \Model_Blueprint
    {
        if (is_string($name)) {
            if (!in_array($name, $this->categories, false))
                $this->categories[] = $name;
        } elseif (is_array($name))
            foreach ($name as $elem)
                $this->category($elem);
        return $this;
    }

    /**
     * Sets a callable condition function to decide weather this is buildable. The function receives the active player as first parameter and must return either TRUE or a string containing the reason why the condition failed.
     * @param callable $c
     * @return Model_Blueprint
     */
    public function condition($c): \Model_Blueprint
    {
        $this->build_condition = $c;
        return $this;
    }

    /**
     * Sets a callable condition function to decide weather this blueprint should be visible. The function receives the active player as first parameter and must return either TRUE or FALSE.
     * @param callable $c
     * @return Model_Blueprint
     */
    public function show_condition($c): \Model_Blueprint
    {
        $this->opt_show_condition = $c;
        return $this;
    }

    /**
     * Setter / Getter for the blueprint description
     * @param null|string $description New name
     * @return Model_Blueprint|string
     */
    public function description($description = null) {
        if ($description === null)
            return $this->obj_description;
        else {
            $this->obj_description = $description;
            return $this;
        }
    }

    /**
     * Setter / Getter for blueprint ID
     * @param null|string $id
     * @return Model_Blueprint|string
     * @throws Exception When trying to overwrite a previously set ID
     */
    public function id($id = null) {
        if ($id === null)
            return $this->iid;
        else {
            if ($this->iid) throw new LogicException('Attempt to rebind blueprint ID!');
            $this->iid = $id;
            $this->provide($id);
            return $this;
        }
    }

    /**
     * Setter / Getter for blueprint ID
     * @param null|string $id
     * @return Model_Blueprint|string|null
     * @throws Exception When trying to overwrite a previously set ID
     */
    public function room($id = null) {
        if ($id === null)
            return $this->is_room ? $this->iid : null;
        else {
            if ($this->iid) throw new LogicException('Attempt to rebind blueprint ID!');
            $this->iid = $id;
            $this->is_room = true;
            $this->provide_room($id);
            $this->global_blocking(true);
            return $this;
        }
    }

    /**
     * @param bool|null $v
     * @return Model_Blueprint|bool
     * @throws Exception When using this function on a non-room
     */
    public function clear_previous_room($v = null) {
        if (!$this->is_room) throw new LogicException('Using CLEAR on non-room!');

        if ($v === null) return $this->room_clear;
        else {
            $this->room_clear = $v;
            return $this;
        }
    }

    public function silence_room_usage($b = null) {
        if (!$this->is_room) throw new LogicException('Using SILENCE on non-room!');

        if ($b === null) return !$this->obj_copy_usage;
        else {
            $this->obj_copy_usage = !$b;
            return $this;
        }
    }

    /**
     * @param bool|null $v
     * @return Model_Blueprint|bool
     * @throws Exception When using this function on a non-room
     */
    public function replace_room_satisfaction($v = null) {
        if (!$this->is_room) throw new LogicException('Using FULL_REPLACE on non-room!');

        if ($v === null) return $this->room_clear_sat;
        else {
            $this->room_clear_sat = $v;
            return $this;
        }
    }

    /**
     * @param array $remove
     * @param array $add
     *
     * @return Model_Blueprint
     */
    public function replace_room_tags(array $remove, array $add): Model_Blueprint {
        if (!$this->is_room) throw new LogicException('Using TAG_REPLACE on non-room!');

        $this->remove_tags = array_merge($remove, $this->remove_tags);
        $this->add_tags = array_merge($add, $this->add_tags);

        return $this;
    }

    /**
     * Setter / Getter for blueprint effect
     * @param null|Model_Effect $effect
     * @return Model_Blueprint|Model_Effect
     */
    public function effect($effect = null) {
        if ($effect === null)
            return $this->effect;
        else {
            $this->effect = $effect;
            return $this;
        }
    }

    /**
     * Setter / Getter for blueprint final message
     * @param null|string $m
     * @return Model_Blueprint|string
     */
    public function message($m = null) {
        if ($m === null)
            return $this->user_message;
        else {
            $this->user_message = $m;
            return $this;
        }
    }

    /**
     * Setter / Getter for the amount of times this blueprint must be build before it actually counts. If set to 0 or smaller, this blueprint will NOT provide anything (and can therefore be build indefinitely)!
     * @param int $s
     * @return Model_Blueprint|int
     */
    public function steps($s) {
        if ($s === null)
            return $this->steps;
        else {
            $this->steps = $s;
            return $this;
        }
    }

    /**
     * Returns the completing level of this blueprint
     *
     * @param string[] $precondition
     *
     * @return bool|int TRUE, when all conditions are met to make a final build, otherwise a number representing the last completed building step. If no steps were build yet, 0 is returned.
     * @throws Exception
     */
    private function completion($precondition) {
        if ($this->steps === 1 || in_array($this->id() . ':' . ($this->steps - 1), $precondition))
            return true;
        else
            for ($i = $this->steps - 2; $i > 0; $i--)
                if (in_array($this->id() . ':' . $i, $precondition))
                    return $i;
        return 0;
    }

    /**
     * Setter / Getter for required energy to produce this blueprint
     * @param null|number $energy
     * @return Model_Blueprint|number
     */
    public function energy($energy = null) {
        if ($energy === null)
            return $this->energy;
        else {
            $this->energy = $energy;
            return $this;
        }
    }

    /**
     * Setter / Getter for the additional defense provided by this blueprint
     * @param null|number $d
     * @return Model_Blueprint|int
     */
    public function defense($d = null) {
        if ($d === null)
            return $this->defense;
        else {
            $this->defense = $d;
            return $this;
        }
    }

    /**
     * Adds a new required item to the stack
     *
     * @param string|array  $class   Required item class
     * @param int           $count   Item count
     * @param null|callable $decider Decider function called for each item instance; will be ignored when items are passed as array!
     * @param null          $type
     *
     * @return Model_Blueprint
     */
    public function material($class, $count = 1, $decider = null, $type = null): \Model_Blueprint
    {
        if (is_array($class))
            foreach ($class as $i_class => $i_count)
                $this->material($i_class, $i_count * $count,$decider,$type);

        else  {
            $inst = new Struct_ItemMaterial();
            $inst->class = $class;
            $inst->count = $count;
            $inst->type = $type;
            $inst->decider = is_callable($decider) ? $decider : null;
            $this->items[] = $inst;
        }
        return $this;
    }

    /**
 * Adds a new previous blueprint requirement. If the given value is an array, all requirements in it are interpreted as alternatives (OR). If called without argument, it returns all IDs this blueprint requires
 * @param string|string[] $rid,... Requirement (can be a blueprint ID or any string that is provided by any other blueprint
 * @return Model_Blueprint|string[][]
 */
    public function requires($rid) {
        if (func_num_args() > 1) {
            foreach (func_get_args() as $arg)
                $this->requires($arg);
            return $this;
        }

        if ($rid === null)
            return $this->requires;
        if (!is_array($rid)) $rid = [$rid];
        if (count($rid))
            $this->requires[] = $rid;
        return $this;
    }

    /**
     * Adds a new previous blueprint requirement for the same room. If the given value is an array, all requirements in it are interpreted as alternatives (OR). If called without argument, it returns all IDs this blueprint requires
     * @param string|string[] $rid,... Requirement (can be a blueprint ID or any string that is provided by any other blueprint
     * @return Model_Blueprint|string[][]
     */
    public function requires_local($rid) {
        if (func_num_args() > 1) {
            foreach (func_get_args() as $arg)
                $this->requires_local($arg);
            return $this;
        }

        if ($rid === null)
            return $this->requires_local;
        if (!is_array($rid)) $rid = [$rid];
        if (count($rid))
            $this->requires_local[] = $rid;
        return $this;
    }

    /**
     * Adds new room requirements or returns the current requirements.
     * @param string|string[] $rid,...
     * @return Model_Blueprint|string[]
     */
    public function requires_room($rid = null) {
        if (func_num_args() > 1) {
            foreach (func_get_args() as $arg)
                $this->requires_room($arg);
            return $this;
        }

        if ($rid === null)
            return $this->room_requirements;

        if (!is_array($rid)) $rid = [$rid];
        $this->room_requirements = array_unique(array_merge($this->room_requirements, $rid));
        return $this;
    }

    /**
     * Adds new room tag requirements or returns the current requirements.
     * @param string|string[] $tags,...
     * @return Model_Blueprint|string[]
     */
    public function requires_room_tag($tags = null) {
        if (func_num_args() > 1) {
            foreach (func_get_args() as $arg)
                $this->requires_room_tag($arg);
            return $this;
        }

        if ($tags === null)
            return $this->requires_tags;

        if (!is_array($tags)) $tags = [$tags];
        $this->requires_tags = array_unique(array_merge($this->requires_tags, $tags));
        return $this;
    }

    /**
     * Adds a new provided ID. Note that the ID if this blueprint is always provided by default. If called without argument, it returns all IDs this blueprint provides
     * @param string $rid Provided ID
     * @return Model_Blueprint|string[]
     * @throws Exception When attempting to add provided IDs to a room blueprint.
     */
    public function provide($rid = null) {
        if ($rid === null)
            return $this->steps <= 0 ? [] : $this->provides;
        if ($this->is_room) throw new LogicException ('Attempt to use PROVIDING with rooms!');
        if (!in_array($rid, $this->provides, false))
            $this->provides[] = $rid;
        return $this;
    }

    /**
     * Adds a new provided ID. Note that the ID if this blueprint is always provided by default. If called without argument, it returns all IDs this blueprint provides
     * @param string|string[] $rids Provided ID or IDs
     * @return Model_Blueprint|string[]
     * @throws Exception When attempting to add provided IDs to a room blueprint.
     */
    public function provide_room($rids = null) {
        if ($rids === null)
            return $this->provides_room;
        if (!$this->is_room) throw new LogicException('Attempt to use ROOM-PROVIDING with non-rooms!');
        if (!is_array($rids)) $rids = [$rids];

        foreach ($rids as $rid)
            if (!in_array($rid, $this->provides_room, false))
                $this->provides_room[] = $rid;
        return $this;
    }

    /**
     * Adds a new removed ID.
     * @param string $rid Provided ID
     * @return Model_Blueprint|string[]
     * @throws Exception When attempting to add removed IDs to a room blueprint.
     */
    public function remove($rid = null) {
        if ($rid === null)
            return $this->removes;
        if ($this->is_room) throw new LogicException('Attempt to use REMOVING with rooms!');
        if (!in_array($rid, $this->removes,false))
            $this->removes[] = $rid;
        return $this;
    }

    /**
     * Adds an item to the producer stack
     *
     * @param string|array $item  Item class
     * @param int          $count Item count
     * @param null         $type
     *
     * @return Model_Blueprint
     */
    public function produces($item, $count = 1, $type = null): \Model_Blueprint
    {
        if (is_array($item)) {
            foreach ($item as $i_class => $i_count)
                $this->produces($i_class, $i_count);
            return $this;
        }

        $inst = new Struct_ItemEntry();
        $inst->class = $item;
        $inst->count = $count;
        $inst->type = $type;
        $this->produces[] = $inst;

        return $this;
    }

    /**
     * Adds a producer function to the producer stack
     * @param callable|[callable] $callable
     * @return Model_Blueprint
     */
    public function produces_advanced(callable $callable): \Model_Blueprint
    {
        if (is_array($callable)) {
            foreach ($callable as $func)
                $this->produces_advanced($func);
            return $this;
        }

        $this->produces_advanced[] = $callable;

        return $this;
    }

    /**
     * Adds an item to the emplacement stack
     * @param string|array $item Item class
     * @param int $count Item count
     * @return Model_Blueprint
     */
    public function emplaces($item, $count = 1): \Model_Blueprint
    {
        if (is_array($item)) {
            foreach ($item as $i_class => $i_count)
                $this->emplaces($i_class, $i_count);
            return $this;
        }

        if (!isset($this->emplaces[$item]))
            $this->emplaces[$item] = $count;
        else $this->emplaces[$item] += $count;

        return $this;
    }

    /**
     * Adds an item to the emplacement stack
     * @param string $text
     * @param null $text_desc
     * @param string $custom_popup
     * @param string $custom_action_id
     * @return Model_Blueprint
     */
    public function emplaces_action($text = 'Herstellen...', $text_desc = null, $custom_popup = 'maker', $custom_action_id = 'lc_lazy_maker'): \Model_Blueprint
    {
        $this->emplaces_action_data[] = [$text,$text_desc,$custom_popup,$custom_action_id];
        return $this;
    }

    /**
     * Getter / Setter for the amount of zombies that are killed by building this blueprint.
     * @param bool $optional Set true if destroying zombies is not the primary function of this blueprint (meaning it can be constructed even if there are no zombies)
     * @param int|array|callable $min Minimal number of kills OR an array containing both min and max numbers as first an second elements OR a function that receives the number of present zombies as well as the active player as an argument and must return a single number or an array containing min/max numbers
     * @param int|null $max Maximum number of kills; only possible if the first parameter is an int
     * @return Model_Blueprint
     */
    public function zombies($optional, $min,$max = null): \Model_Blueprint
    {
        if (is_array($min))
            return $this->zombies($optional,$min[0],$min[1]);

        $this->zombies_optional = $optional;

        if (is_callable($min))
            $this->zombie_kills = $min;
        else
            $this->zombie_kills = ($max !== null) ? [$min, max($min,$max)] : $min;

        return $this;
    }

    /**
     * Modifies the decay values of the location; only has an effect if the location is a hideout, otherwise these variables will be discarded
     * @param number $decay_dif Decay difference (positive values INCREASE decay)
     * @param int $speed_dif Decay speed difference (positive values INCREASE decay speed)
     * @return Model_Blueprint
     */
    public function decay($decay_dif, $speed_dif = 0): \Model_Blueprint
    {
        $this->decay = $decay_dif;
        $this->decay_speed = $speed_dif;
        return $this;
    }

    /**
     * Modifies the decoration value of the location; only has an effect if the location is a hideout, otherwise these variables will be discarded
     * @param int $new Added deco value
     * @return Model_Blueprint
     */
    public function deco($new): \Model_Blueprint
    {
        $this->deco_value = $new;
        return $this;
    }

    /**
 * @param string[] $preconditions
 * @param Model_Room|null $room
 * @return bool
 */
    private function can_prod($preconditions, $room = null): bool
    {
        if ($this->is_room) return $this->can_prod_room($room, $preconditions);
        if ($this->steps > 0) {
            if ($room === null && !$this->global_blocking()) return true;
            foreach ($this->provides as $p)
                if (in_array($p, $this->global_blocking() ? $preconditions : $room->get_content(), false))
                    return false;
        }
        return true;
    }

    /**
     * @param Model_Room|null $room
     * @param array $preconditions
     * @return bool
     */
    private function can_prod_room($room = null, $preconditions = []): bool
    {
        if (!$this->use_global_blocking && $room === null) return true;
        foreach ($this->provides_room as $p)
            if (in_array($p, $this->use_global_blocking ? $preconditions : $room->get_content(), true))
                return false;

        if ($room === null) return true;

        foreach ($this->provides_room as $p)
            if ($room->check_room_satisfaction($p) && !in_array($p, $this->requires_room(), true))
                return false;

        return true;
    }

    private function can_req($preconditions, $local = false): bool
    {
        foreach ($local ? $this->requires_local : $this->requires as $r_block) {
            foreach ($r_block as $requirement)
                if (in_array($requirement, $preconditions, false))
                    continue 2;
            return false;
        }
        return true;
    }

    /**
     * @param Model_Room $room
     * @return bool
     */
    private function can_room($room): bool
    {
        $t = true;
        foreach ($this->requires_tags as $tag) if (!$room->has_tag($tag)) $t = false;
        return $t &&  $room->check_room_satisfaction($this->requires_room()) && $this->can_req($room->get_content(), true);
    }

    /**
     * Returns true, when the blueprint can be realized given the preconditions
     * @param string[] $preconditions Realized blueprints
     * @param Model_Room|null $room
     * @param bool $ignore_blocked_slots Set true if you want to ignore blocked slots
     * @return bool
     */
    public function can($preconditions, $room = null, $ignore_blocked_slots = false): bool
    {
        return ($ignore_blocked_slots || $this->can_prod($preconditions,$room)) && $this->can_req($preconditions) && ($room === null || $this->can_room($room, $preconditions));
    }


    /**
     * @param Model_Player $player Active player
     * @param string[] $preconditions Realized blueprints
     * @param Model_Room $room
     * @return bool|string[] Returns if execution failed, or an array containing the newly activated blueprint ids. Note that this function may return an empty array on success!
     * @throws Exception
     */
    public function execute($player, $preconditions, $room) {
        if ($this->opt_show_condition) {
            $c = $this->opt_show_condition;
            if ($c($player) !== true)
                return false;
        }

        if ($this->build_condition) {
            $c = $this->build_condition;
            if (($tmp = $c($player)) !== true) {
                $player->log()->add($tmp);
                return false;
            }
        }

        if (!$player->get_status()->has(Model_Status::MS_STAT_ENERGY, $this->energy, Model_Status::MS_EFFECT_REQUIREMENT)) {
            $player->log()->add('Du bist derzeit nicht in der Lage diese Aktion durchzuführen.');
            return false;
        }

        if (!$this->can($preconditions, $room)) {
            $player->log()->add('Nicht alle Vorraussetungen für diese Aktion sind erfüllt.');
            return false;
        }

        if ($this->space() > $room->get_space(true)) {
            $player->log()->add('Für diese Aktion fehlt es an freiem Platz.');
            return false;
        }

        if ($this->zombie_kills && !$this->zombies_optional &&!$player->location()->zombie_pop()) {
            $player->log()->add('Es ist verständlich dass du gerne irgend etwas töten möchtest... nur sind leider gerade keine Zombies in der Nähe.');
            return false;
        }

        if (!Tool_Scripts::consume_items($this->items, Struct_ScriptItemSource::default()->use_perspective($player)->use_decider($this->get_item_decider()), true)) {
            $player->log()->add('Dir fehlen Gegenstände, um diese Aktion durchzuführen.');
            return false;
        }

        $raw_item_objects = [];

        $player->get_status()->modify(Model_Status::MS_STAT_ENERGY, -$this->energy, Model_Status::MS_EFFECT_REQUIREMENT);
        $basic_producer_stack = $this->produces;


        foreach ($this->produces_advanced as $callable) {
            $entry = $callable($player,true);
            if (!is_array($entry)) $entry = [$entry];
            foreach ($entry as $id => $sub) {
                if (Tool_System::instance_of($sub, 'Struct_ItemEntry')) {
                    $basic_producer_stack[] = $sub;
                    continue;
                }

                if (is_object($sub) && Tool_System::instance_of($sub,Model_Items_Abstract_Item::cls())) {
                    $raw_item_objects[] = $sub;
                    continue;
                }

                if (is_string($sub)) {
                    $id = $sub;
                    $sub = 1;
                }

                $inst = new Struct_ItemEntry();
                $inst->class = $id;
                $inst->count = $sub;
                $basic_producer_stack[] = $inst;
            }
        }

        foreach ($basic_producer_stack as $item)
            for ($i = 0; $i < $item->count; $i++)
                $raw_item_objects[] = new $item->class( $item->type );

        foreach ($raw_item_objects as $instance) {
            $player->location()->inventory()->add($instance);

            /** @noinspection NotOptimalIfConditionsInspection */
            if (Tool_System::instance_of($instance, Model_Items_Abstract_Virtual::cls()) && $instance::setup_location())
                $instance->set_location_info($player->location_class());
        }


        $ret = $this->apply($player->location(), $preconditions, $room);


        if ($this->effect)
            $this->effect->execute($player, null);

        if ($this->zombie_kills) {
            $z = $this->zombie_kills;
            if (is_callable($z))
                $z = $z($player->location()->zombie_pop(), $player);

            if (is_numeric($z))
                $z = [$z,$z];

            $z = min(random_int($z[0], $z[1]), $player->location()->zombie_pop());

            if (!$z) $player->log()->add('Mist... du hast nicht mal einen einzigen Zombie umgebracht.');
            else {
                $player->log()->add('Du hast :num Zombies vernichtet!', array(':num' => $z));
                $player->achievements()->achieve(Model_Achievement::MA_KILLED_ZOMBIES, $z);
                $player->location()->zombie_factory()->reduce_accum($z);
            }
        } elseif ($this->user_message) $player->log()->add($this->user_message);

        return $ret;
    }

    /**
     * @param Model_Room                  $room
     * @param Model_Places_Abstract_Place $location
     *
     * @return array
     * @throws Exception
     */
    private function apply_room($room,$location): array
    {
        if ($this->clear_previous_room()) $room->clear();

        $room->upgrade($this->silence_room_usage() ? null : $this->obj_name,$this->clear_previous_room() ? true : ($this->replace_room_satisfaction() ? $this->requires_room() : false),$this->provide_room());

        foreach ($this->emplaces_action_data as [$text,$text_desc,$custom_popup,$custom_action_id
        ]
        ) {
            $room->inventory()->add(new Model_Items_Virtual_Location_Room_Generic($text,$text_desc,$custom_popup,$custom_action_id));
        }

        $room->remove_tag($this->remove_tags);
        $room->add_tag($this->add_tags);

        foreach ($this->emplaces as $item => $count)
            for ($i = 0; $i < $count; $i++) {
                $instance = new $item();
                $room->inventory()->add($instance);

                /** @var Model_Items_Abstract_Virtual $instance */
                /** @noinspection NotOptimalIfConditionsInspection */
                if (Tool_System::instance_of($instance, Model_Items_Abstract_Virtual::cls()) && $instance::setup_location())
                    $instance->set_location_info($location->uin(), $room->id());
            }

        return [];
    }

    /**
     * @param Model_Places_Abstract_Place $location
     * @param string[]                    $preconditions Realized blueprints
     * @param Model_Room                  $room
     *
     * @return bool|string[] Returns false if execution failed, or an array containing the newly activated blueprint ids. Note that this function may return an empty array on success!
     * @throws Exception
     */
    public function apply($location, $preconditions, $room) {
        if (Tool_System::instance_of($location, 'Model_Places_Abstract_Hideout')) {
            /** @var Model_Places_Abstract_Hideout $location */
            $location->set_decay($this->decay/100, false);
            $location->set_patchup($this->decay_speed, false);

            $room->modify_defense( $this->defense );
            $room->modify_deco( $this->deco_value );
        }

        if (!$room->deduct_space($this->space())) throw new LogicException("Precondition failed: SPACE_CALCULATION");

        if ($this->is_room) return $this->apply_room($room,$location);

        if ($this->steps <= 0)
            if ($this->remove()) {
                $ret = [];
                foreach ($this->remove() as $rem)
                    $ret[] = "-{$rem}";
            } else return [];

        $ret = $this->provide();

        if (($c = $this->completion($this->global_blocking() ? $preconditions : $room->get_content())) !== true) {
            foreach ($ret as &$r)
                $r = $r . ':' . ($c+1);
            unset($r);
        }


        foreach ($this->remove() as $rem)
            $ret[] = "-{$rem}";

        return $ret;
    }

    /**
     * @param Struct_ItemEntry[] $data
     *
     * @return array
     * @throws Exception
     */
    private function materialize($data): array
    {
        $tmp = [];
        foreach ($data as $entry) {
            /** @var Model_Items_Abstract_Item $class */
            $class = $entry->class;
            if (!Tool_System::instance_of(
                $class, Model_Items_Abstract_Virtual::cls()
            )
            ) {
                $tmp[] = [
                    'name' => $entry->name(),
                    'icon' => $entry->icon(),
                    'count' => $entry->count,
                    'have' => Tool_Scripts::count_items($class,Struct_ScriptItemSource::default()->use_decider(function (Model_Items_Abstract_Item $item) use ($entry) {
                        return $entry->type === null ? true : ($item->type === $entry->type);
                    }))
                ];
            }
        }
        return $tmp;
    }

    /**
     * @param string[] $preconditions
     * @param Model_Room $room
     * @param Model_Player $player
     * @return array
     * @throws Exception
     */
    public function compile($preconditions, $room, $player): array
    {
        $current_steps = $this->completion($preconditions);
        $still_open = $this->can_prod($preconditions, $room);
        $requirements_fulfilled = $this->can_req($preconditions) && $this->can_room($room);

        if ($this->zombie_kills) {
            $z = $this->zombie_kills;
            if (is_callable($z))
                $z = $z($player->location()->zombie_pop(), $player);

            if (is_numeric($z))
                $z = [$z,$z];
        } else $z = false;

        $hidden = false;
        if ($this->opt_show_condition) {
            $c = $this->opt_show_condition;
            if ($c($player) !== true)
                $hidden = true;
        }

        if ($this->obj_name)
            $name = $this->obj_name;
        elseif (count($this->produces))
            $name = $this->produces[0]->name();
        else $name = '???';

        $room_data = $tag_data = [];
        foreach ($this->room_requirements as $rq_room)
            $room_data[$rq_room] = $room->check_room_satisfaction($rq_room);
        foreach ($this->requires_tags as $tag) {
            $tag_data[$tag] = ['name' => Model_Room::tag_info($tag), 'b' => $room->has_tag($tag)];
            if (!$room->has_tag($tag)) $requirements_fulfilled = false;
        }

        $requirements_fulfilled = $requirements_fulfilled && ($room->get_space(true) >= $this->space());

        $room_occ_data = [];
        foreach ($this->provide_room() as $occ_room)
            if (!in_array($occ_room, $this->requires_room(), false))
                $room_occ_data[$occ_room] = !$room->check_room_satisfaction($occ_room);

        $basic_producer_stack = $this->produces;
        foreach ($this->produces_advanced as $callable) {
            $entry = $callable($player,false);
            if (!is_array($entry)) $entry = [$entry];
            foreach ($entry as $id => $sub) {
                if (Tool_System::instance_of($sub, 'Struct_ItemEntry')) {
                    $basic_producer_stack[] = $sub;
                    continue;
                }
                if (is_string($sub)) {
                    $id = $sub;
                    $sub = 1;
                }

                $inst = new Struct_ItemEntry();
                $inst->class = $id;
                $inst->count = $sub;
                $basic_producer_stack[] = $inst;
            }
        }

        return [
            'id' => $this->iid,
            'is_room' => $this->is_room,
            'globally_blocked' => $this->global_blocking(),
            'name' => $name,
            'categories' => $this->categories,
            'description' => $this->obj_description,
            'requires' => $this->requires,
            'requires_room' => $room_data,
            'requires_tag' => $tag_data,
            'requires_local' => $this->requires_local,
            'space' => $this->space(),
            'energy' => $this->energy,
            'repair' => -$this->decay,
            'decay_speed' => $this->decay_speed === 0 ? 0 : ($this->decay_speed > 0 ? 1 : -1),
            'defense' => $this->defense,
            'deco' => $this->deco_value,
            'material_in' => $this->materialize($this->items),
            'material_out' => $this->materialize($basic_producer_stack),
            'build' => in_array($this->iid,$preconditions, false),
            'build_local' => in_array($this->iid,$room->get_content(), false),
            'slot_open' => $still_open,
            'space_open' => $room->get_space(true) >= $this->space(),
            'build_possible' => $requirements_fulfilled,
            'steps_max' => $this->steps,
            'steps_current' => ($current_steps === true) ? $this->steps - 1 : $current_steps,
            'occupies' => $this->provide(),
            'occupies_room' => $room_occ_data,
            'hidden' => $hidden,
            'zombies' => $z,
            'confirm' => $this->confirmation
        ];
    }

}