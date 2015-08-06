<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Combat_Field {

    /** @var Model_Combat_Actor[] */
    private $combatants = [];

    private $field = [64,40];

    /** @var  Model_Combat_Scene */
    private $scene;

    /**
     * @return Model_Combat_Field
     */
    public static function factory() {
        return new Model_Combat_Field();
    }

    public function __construct() {
        $this->scene = new Model_Combat_Scene();
    }

    /**
     * Counts the amount of different fractions in this battle.
     * @param bool $limit_alive If true (default), only fractions with still alive combatants will be counted.
     * @return int
     */
    private function get_distinct_groups($limit_alive = true) {
        $gp = [];
        foreach ($this->combatants as $combatant)
            if (!$limit_alive || $combatant->alive())
                $gp[$combatant->group()] = true;
        return count($gp);
    }

    /**
     * @param int $group
     * @param Model_Combat_Actor|Model_Combat_Actor[] $combatant
     * @return Model_Combat_Field
     */
    public function add_combatant($group, $combatant) {
        if (is_array($combatant)) {
            foreach ($combatant as $c)
                $this->add_combatant($group, $c);
        } else
            $this->combatants[] = $combatant->set_scene($this->scene)->group($group)->id(count($this->combatants) + 1);

        return $this;
    }

    /**
     * @return Model_Combat_Actor|null
     */
    private function jump_next_move() {
        $a = min(array_map(function($a) {
            /** @var $a Model_Combat_Actor */
            return $a->alive() ? $a->next_step_counter() : PHP_INT_MAX;
        }, $this->combatants));

        if ($a == PHP_INT_MAX) return null;

        foreach ($this->combatants as $combatant)
            if ($combatant->alive())
                $combatant->next_step_counter(-$a);

        foreach ($this->combatants as $combatant)
            if ($combatant->alive() && !$combatant->next_step_counter())
                return $combatant;

        return null;
    }

    /**
     * @return Model_Combat_Field
     */
    public function begin() {
        // Add combatants to the scene
        foreach ($this->combatants as $combatant)
            $this->scene->add_combatant($combatant);

        $round = 0;
        while ($round < 512 && $this->get_distinct_groups() > 1) {
            $round++;

            if (!($next = $this->jump_next_move()))
                break;

            $this->scene->next_combatant($next);
            $next->act(array_filter($this->combatants, function($a) use ($next) {
                /** @var $a Model_Combat_Actor */
                return $a->alive() && $a->group() == $next->group();
            }), array_filter($this->combatants, function($a) use ($next) {
                /** @var $a Model_Combat_Actor */
                return $a->alive() && $a->group() != $next->group();
            }));
        }

        return $this;
    }

    /**
     * @return Model_Combat_Scene
     */
    public function get_scene() {
        return $this->scene;
    }

    /**
     * @param $avg_distance
     * @param int $jitter
     * @return Model_Combat_Field
     */
    public function init_positions($avg_distance, $jitter = 3) {
        $groups = $this->get_distinct_groups(false);
        if ($groups > 1)
            $avg_distance = max(0,min($this->field[0]/($groups-1), $avg_distance));

        $gp_tmp = [];
        $gp_acc = 0;

        foreach ($this->combatants as $combatant) {
            if (isset($gp_tmp[$combatant->group()]))
                $t = $gp_tmp[$combatant->group()];
            else {
                $t = $gp_acc++;
                $gp_tmp[$combatant->group()] = $t;
            }

            $combatant->position([$t * $avg_distance + mt_rand(-$jitter, $jitter), mt_rand(0, $this->field[1])]);
        }

        return $this;
    }
}