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
    private $requires = [];
    private $provides = [];
    private $energy = 0;

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
     * Adds a new required item to the stack
     * @param string $class Required item class
     * @param int $count Item count
     * @return Model_Blueprint
     */
    public function material($class, $count) {
        $this->items[$class] = $count;
        return $this;
    }

    /**
     * Adds a new previous blueprint requirement. If the given value is an array, all requirements in it are interpreted as alternatives (OR). If called without argument, it returns all IDs this blueprint requires
     * @param string|string[] $rid Requirement (can be a blueprint ID or any string that is provided by any other blueprint
     * @return Model_Blueprint|string[][]
     */
    public function requires($rid) {
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
            return $this->provides;
        elseif (!in_array($rid, $this->provides))
            $this->provides[] = $rid;
        return $this;
    }

    /**
     * Returns true, when the blueprint can be realized given the preconditions
     * @param string[] $preconditions Realized blueprints
     * @return bool
     */
    public function can($preconditions) {
        foreach ($this->requires as $r_block) {
            foreach ($r_block as $requirement)
                if (in_array($requirement, $preconditions))
                    continue(2);
            return false;
        }
        return true;
    }

    /**
     * @param Model_Player $player Active player
     * @param string[] $preconditions Realized blueprints
     * @return bool|string[] Returns if execution failed, or an array containing the newly activated blueprint ids. Note that this function may return an empty array on success!
     */
    public function execute($player, $preconditions) {
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
        if ($this->effect)
            $this->effect->execute($player, null);

        return $this->provide();
    }

}