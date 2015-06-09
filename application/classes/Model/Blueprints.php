<?php

class Model_Blueprints {

    /**
     * @var Model_Blueprint[] $externals
     * @var Model_Blueprint[] $blueprints
     */
    private $externals = [];
    private $blueprints = [];
    private $processor_stack = [];

    /**
     * @param Model_Places_Abstract_Place|null $location
     * @param string|null $category
     * @return Model_Blueprints
     */
    public static function factory($location = null, $category = null) {
        $ret = new Model_Blueprints();
        if (!$location || !$category) return $ret;
        else {
            foreach (Tool_System::get_class_hierarchy($location) as $name) {
                $name = str_replace('Model_Places_','',$name, $n);
                if ($n == 1 && $b = Tool_System::simple_config("blueprints/{$category}/" . $name))
                    /** @var Model_Blueprints $b */
                    $ret->merge($b,true);
            }

            return $ret;
        }
    }

    /**
     * @param Model_Places_Abstract_Place|null $location
     * @param string|null $category
     * @param string|string[] $id
     * @return bool|string[]
     */
    public static function fast_apply($location, $category, $id) {
        $b = static::factory($location, $category);
        if (!is_array($id)) $id = [$id];
        $ret = [];
        foreach ($id as $entry) {
            $tmp = $b->perform_apply($entry, $location);
            if (is_array($tmp))
                $ret = array_merge($ret, $tmp);
        }

        return $ret;
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
     * @return Model_Blueprints
     */
    public function merge($other, $dominance = false) {
        $this->blueprints = $dominance ? array_merge($other->blueprints, $this->blueprints) : array_merge($this->blueprints, $other->blueprints);
        $this->externals = $dominance ? array_merge($other->externals, $this->externals) : array_merge($this->externals, $other->externals);

        foreach (array_keys($this->blueprints) as $id)
            unset($this->externals[$id]);
        return $this;
    }

    /**
     * @return Model_Blueprints
     */
    public function externalize() {
        $this->externals = array_merge($this->externals, $this->blueprints);
        $this->blueprints = [];

        return $this;
    }

    /**
     * Validates a blueprint group; this function will throw an exception when the group is invalid. This function also drops the stack.
     * @return Model_Blueprints
     * @throws Exception
     */
    public function validate() {
        $this->drop_stack();
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
                if ($blueprint->can($preconditions, true)) {
                    $deadlock = false;
                    $preconditions = array_merge($preconditions, $blueprint->provide());
                    return false;
                } else return true;
            });
        }

        if ($deadlock) throw new Exception('Unable to compile blueprint group: Group contains blueprints with unresolvable requirements.');
        return $this;
    }

    public function compile($preconditions, $player) {
        $ret = [];
        foreach ($this->externals as $b)
            /** @var Model_Blueprint $b */
            $ret[$b->id()] = array_merge($b->compile($preconditions, $player), [
                'hidden' => true
            ]);
        foreach ($this->blueprints as $b) {
            /** @var Model_Blueprint $b */
            $tmp = $b->modify($player, $preconditions)->compile($preconditions, $player);
            if (!$tmp['hidden'])
                $ret[$b->id()] = $tmp;
        }

        return $ret;
    }

    /**
     * @param string $id
     * @param Model_Player $player
     * @param string[] $preconditions
     * @return bool|string[]
     */
    public function execute($id, $player, $preconditions) {
        if (!isset($this->blueprints[$id]))
            return false;
        else {
            /** @var Model_Blueprint $b */
            $b = $this->blueprints[$id];
            $r = $b->modify($player, $preconditions)->execute($player, $preconditions);
            if (is_array($r))
                $player->location()->add_upgrades($r);
            return $r;
        }
    }

    /**
     * @param string $id
     * @param Model_Places_Abstract_Place $location
     * @return bool|string[]
     */
    private function perform_apply($id, $location) {
        $r = false;
        if (!isset($this->blueprints[$id]) && !isset($this->externals[$id]))
            return false;
        elseif (isset($this->blueprints[$id])) {
            /** @var Model_Blueprint $b */
            $b = $this->blueprints[$id];
            $r = $b->apply($location, $location->get_upgrades());
        }
        else $r = [$id];

        if (is_array($r))
            $location->add_upgrades($r);
        return $r;
    }
}