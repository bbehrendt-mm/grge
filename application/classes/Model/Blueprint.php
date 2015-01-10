<?php

class Model_Blueprint {

    /**
     * @var Model_Effect $effect
     */
    private $effect;

    private $name;
    private $id;
    private $description;

    private $items = [];
    private $produces = [];
    private $requires = [];
    private $provides = [];
    private $energy = 0;
    private $decay = 0;
    private $decay_speed = 0;
    private $steps = 1;
    private $condition;
    private $message;
    private $defense = 0;

    /**
     * Creates a new blueprint instance
     * @return Model_Blueprint
     */
    public static function factory() {
        return new Model_Blueprint();
    }

    /**
     * Setter / Getter for the blueprint name
     * @param null|string $name New name
     * @return Model_Blueprint|string
     */
    public function name($name = null) {
        if ($name === null)
            return $this->name;
        else {
            $this->name = $name;
            return $this;
        }
    }

    /**
     * Sets a callable condition function to decide weather this is buildable. The function receives the active player as first parameter and must return either TRUE or a string containing the reason why the condition failed.
     * @param callable $c
     * @return Model_Blueprint
     */
    public function condition($c) {
        $this->condition = $c;
        return $this;
    }

    /**
     * Setter / Getter for the blueprint description
     * @param null|string $description New name
     * @return Model_Blueprint|string
     */
    public function description($description = null) {
        if ($description === null)
            return $this->description;
        else {
            $this->description = $description;
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
            return $this->id;
        else {
            if ($this->id) throw new Exception('Attempt to rebind blueprint ID!');
            $this->id = $id;
            $this->provide($id);
            return $this;
        }
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
            return $this->message;
        else {
            $this->message = $m;
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
     * @param string[] $precondition
     * @return bool|int TRUE, when all conditions are met to make a final build, otherwise a number representing the last completed building step. If no steps were build yet, 0 is returned.
     */
    private function completion($precondition) {
        if ($this->steps == 1 || in_array($this->id() . ':' . ($this->steps - 1), $precondition))
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
     * @param string|array $class Required item class
     * @param int $count Item count
     * @return Model_Blueprint
     */
    public function material($class, $count = 1) {
        if (is_array($class))
            foreach ($class as $i_class => $i_count)
                $this->material($i_class, $i_count);

        else $this->items[$class] = $count;
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
     * Adds a new provided ID. Note that the ID if this blueprint is always provided by default. If called without argument, it returns all IDs this blueprint provides
     * @param string $rid Provided ID
     * @return Model_Blueprint|string[]
     */
    public function provide($rid = null) {
        if ($rid === null)
            return $this->steps <= 0 ? [] : $this->provides;
        elseif (!in_array($rid, $this->provides))
            $this->provides[] = $rid;
        return $this;
    }

    /**
     * Adds an item to the producer stack
     * @param string|array $item Item class
     * @param int $count Item count
     * @return Model_Blueprint
     */
    public function produces($item, $count = 1) {
        if (is_array($item)) {
            foreach ($item as $i_class => $i_count)
                $this->produces($i_class, $i_count);
            return $this;
        }

        if (!isset($this->produces[$item]))
            $this->produces[$item] = $count;
        else $this->produces[$item] += $count;

        return $this;
    }

    /**
     * Modifies the decay values of the location; only has an effect if the location is a hideout, otherwise these variables will be discarded
     * @param number $decay_dif Decay difference (positive values INCREASE decay)
     * @param int $speed_dif Decay speed difference (positive values INCREASE decay speed)
     * @return Model_Blueprint
     */
    public function decay($decay_dif, $speed_dif = 0) {
        $this->decay = $decay_dif;
        $this->decay_speed = $speed_dif;
        return $this;
    }

    private function can_prod($preconditions) {
        if ($this->steps > 0) {
            foreach ($this->provides as $p)
                if (in_array($p, $preconditions))
                    return false;
        }
        return true;
    }

    private function can_req($preconditions) {
        foreach ($this->requires as $r_block) {
            foreach ($r_block as $requirement)
                if (in_array($requirement, $preconditions))
                    continue(2);
            return false;
        }
        return true;
    }

    /**
     * Returns true, when the blueprint can be realized given the preconditions
     * @param string[] $preconditions Realized blueprints
     * @param bool $ignore_blocked_slots Set true if you want to ignore blocked slots
     * @return bool
     */
    public function can($preconditions, $ignore_blocked_slots = false) {
        return ($ignore_blocked_slots || $this->can_prod($preconditions)) && $this->can_req($preconditions);
    }

    /**
     * @param Model_Player $player Active player
     * @param string[] $preconditions Realized blueprints
     * @return bool|string[] Returns if execution failed, or an array containing the newly activated blueprint ids. Note that this function may return an empty array on success!
     */
    public function execute($player, $preconditions) {
        if ($this->condition) {
            $c = $this->condition;
            if ($c($player) !== true)
                return false;
        }

        if ($player->stats_get(Model_Player::MP_STAT_ENERGY) < $this->energy) {
            $player->log()->add('Du bist derzeit nicht in der Lage diese Aktion durchzuführen.');
            return false;
        }

        if (!$this->can($preconditions)) {
            $player->log()->add('Nicht alle Vorraussetungen für diese Aktion sind erfüllt.');
            return false;
        }

        if (!Tool_Scripts::consume_available_items($this->items, true, true, false, $player, true)) {
            $player->log()->add('Dir fehlen Gegenstände, um diese Aktion durchzuführen.');
            return false;
        }

        $player->stats_modify(Model_Player::MP_STAT_ENERGY, -$this->energy);
        foreach ($this->produces as $item => $count)
            for ($i = 0; $i < $count; $i++)
                $player->location()->inventory()->add(new $item());
        if (Tool_System::instance_of($player->location(), 'Model_Places_Abstract_Hideout')) {
            /** @var Model_Places_Abstract_Hideout $place */
            $place = $player->location();
            $place->set_decay($this->decay, false);
            $place->set_patchup($this->decay_speed, false);
            $place->inc_defense($this->defense);
        }
        if ($this->effect)
            $this->effect->execute($player, null);

        $player->log()->add($this->message);

        if ($this->steps <= 0)
            return [];

        $ret = $this->provide();
        if (($c = $this->completion($preconditions)) !== true)
            foreach ($ret as &$r)
                $r = $r . ':' . ($c+1);

        return $ret;
    }

    private function materialize($data) {
        $tmp = [];
        foreach ($data as $class => $count)
            /** @var Model_Items_Abstract_Item $class */
            $tmp[] = [
                'name' => $class::static_name(),
                'icon' => $class::static_icon(),
                'count' => $count,
                'have' => Tool_Scripts::count_available_items($class)
            ];
        return $tmp;
    }

    public function compile($preconditions) {
        $current_steps = $this->completion($preconditions);
        $still_open = $this->can_prod($preconditions);
        $requirements_fulfilled = $this->can_req($preconditions);

        if ($this->name)
            $name = $this->name;
        elseif (count($this->produces)) {
            /** @var Model_Items_Abstract_Item $cls */
            $cls = array_keys($this->produces)[0];
            $name = $cls::static_name();
        } else $name = '???';

        return [
            'id' => $this->id,
            'name' => $name,
            'description' => $this->description,
            'requires' => $this->requires,
            'energy' => $this->energy,
            'repair' => -$this->decay,
            'decay_speed' => $this->decay_speed == 0 ? 0 : ($this->decay_speed > 0 ? 1 : -1),
            'defense' => $this->defense,
            'material_in' => $this->materialize($this->items),
            'material_out' => $this->materialize($this->produces),
            'build' => in_array($this->id,$preconditions),
            'slot_open' => $still_open,
            'build_possible' => $requirements_fulfilled,
            'steps_max' => $this->steps,
            'steps_current' => ($current_steps === true) ? $this->steps - 1 : $current_steps,
            'occupies' => $this->provide()
        ];
    }

}