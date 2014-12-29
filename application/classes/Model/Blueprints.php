<?php

class Model_Blueprints {

    /**
     * @var Model_Blueprint[] $externals
     * @var Model_Blueprint[] $blueprints
     */
    private $externals;
    private $blueprints;

    public static function factory() {
        return new Model_Blueprints();
    }

    /**
     * @param Model_Blueprint $blueprint
     * @param bool $external
     * @return Model_Blueprints
     */
    public function add_blueprints($blueprint, $external = false) {
        if ($external)
            $this->externals[$blueprint->id()] = $blueprint;
        else $this->blueprints[$blueprint->id()] = $blueprint;

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
            if (in_array($ret, $blueprint->provide()))
                $ret[] = $blueprint;
        foreach ($this->blueprints as $blueprint)
            /** @var Model_Blueprint $blueprint */
            if (in_array($ret, $blueprint->provide()))
                $ret[] = $blueprint;

        return $ret;
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
                    return true;
                } else return false;
            });
        }

        if ($deadlock) throw new Exception('Unable to compile blueprint group: Group contains blueprints with unresolvable requirements.');
        return $this;
    }
}