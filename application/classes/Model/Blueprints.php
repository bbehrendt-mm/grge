<?php

class Model_Blueprints {

    /**
     * @var Model_Blueprint[] $externals
     * @var Model_Blueprint[] $blueprints
     */
    private $externals = [];
    private $blueprints = [];
    private $processor_stack = [];

    public static function factory() {
        return new Model_Blueprints();
    }

    /**
     * @param callable $c
     * @return Model_Blueprints
     */
    public function push_stack($c) {
        $this->processor_stack[] = $c;
        return $this;
    }

    /**
     * @param int $n
     * @return Model_Blueprints
     */
    public function pop_stack($n = 1) {
        for ($i = 0; $i < $n; $i++)
            array_pop($this->processor_stack);
        return $this;
    }

    /**
     * @return Model_Blueprints
     */
    public function drop_stack() {
        $this->processor_stack = [];
        return $this;
    }

    /**
     * @param Model_Blueprint|string|string[] $blueprint
     * @param bool $external
     * @return Model_Blueprints
     */
    public function add_blueprints($blueprint, $external = false) {
        if (is_string($blueprint))
            return $this->add_blueprints(Model_Blueprint::factory()->id($blueprint), $external);
        elseif (is_array($blueprint) && count($blueprint) > 0)
            return $this->add_blueprints(Model_Blueprint::factory()->id($blueprint[0])->provide(array_slice($blueprint,1)), $external);

        foreach ($this->processor_stack as $post)
            $post($blueprint);

        if ($external) {
            if (isset($this->blueprints[$blueprint->id()]))
                return $this;
            $this->externals[$blueprint->id()] = $blueprint;
        } else {
            $this->blueprints[$blueprint->id()] = $blueprint;
            unset($this->externals[$blueprint->id()]);
        }

        return $this;
    }

    /**
     * @param string $rid
     * @return Model_Blueprint|null
     */
    public function get_blueprint($rid) {
        if (isset($this->blueprints[$rid]))
            return $this->blueprints[$rid];
        elseif (isset($this->externals[$rid]))
            return $this->externals[$rid];
        else return null;
    }

    /**
     * @param string $rid
     * @return Model_Blueprint[]
     */
    public function find_blueprints($rid) {
        $ret = [];
        foreach ($this->externals as $blueprint)
            /** @var Model_Blueprint $blueprint */
            if (in_array($rid, $blueprint->provide()))
                $ret[] = $blueprint;
        foreach ($this->blueprints as $blueprint)
            /** @var Model_Blueprint $blueprint */
            if (in_array($rid, $blueprint->provide()))
                $ret[] = $blueprint;

        return $ret;
    }

    /**
     * @param Model_Blueprints $other
     * @param bool $dominance
     */
    public function merge($other, $dominance = false) {
        $this->blueprints = $dominance ? array_merge($other->blueprints, $this->blueprints) : array_merge($this->blueprints, $other->blueprints);
        $this->externals = $dominance ? array_merge($other->externals, $this->externals) : array_merge($this->externals, $other->externals);

        foreach (array_keys($this->blueprints) as $id)
            unset($this->externals[$id]);
    }

    /**
     * Validates a blueprint group; this function will throw an exception when the group is invalid.
     * @return Model_Blueprints
     * @throws Exception
     */
    public function validate() {
        $preconditions = [];
        foreach ($this->externals as $blueprint)
            /** @var Model_Blueprint $blueprint */
            $preconditions = array_merge($preconditions, $blueprint->provide());

        $cache = $this->blueprints;

        $deadlock = false;
        while (!$deadlock && !empty($cache)) {
            $deadlock = true;
            $cache = array_filter($cache, function($blueprint) use (&$deadlock, &$preconditions) {
                /** @var Model_Blueprint $blueprint */
                if ($blueprint->can($preconditions)) {
                    $deadlock = false;
                    $preconditions = array_merge($preconditions, $blueprint->provide());
                    return false;
                } else return true;
            });
        }

        if ($deadlock) throw new Exception('Unable to compile blueprint group: Group contains blueprints with unresolvable requirements.');
        return $this;
    }

    public function compile($preconditions) {
        $ret = [];
        foreach ($this->externals as $b)
            /** @var Model_Blueprint $b */
            $ret[$b->id()] = array_merge($b->compile($preconditions), [
                'hidden' => true
            ]);
        foreach ($this->blueprints as $b)
            /** @var Model_Blueprint $b */
            $ret[$b->id()] = array_merge($b->compile($preconditions), [
                'hidden' => false
            ]);
        return $ret;
    }

    public function execute($id, $player, $preconditions) {
        if (!isset($this->blueprints[$id]))
            return false;
        else {
            /** @var Model_Blueprint $b */
            $b = $this->blueprints[$id];
            return $b->execute($player, $preconditions);
        }
    }
}