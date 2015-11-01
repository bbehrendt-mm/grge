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
     * @param Model_Combat_Actor|Model_Combat_Actor[]|Model_Player|Model_Player[] $combatant
     * @return Model_Combat_Field
     */
    public function add_combatant($group, $combatant) {
        if (is_array($combatant)) {
            foreach ($combatant as $c)
                $this->add_combatant($group, $c);
        }
        elseif (Tool_System::instance_of($combatant, 'Model_Player'))
            return $this->add_combatant($group, $combatant->create_combatant());
        elseif (Tool_System::instance_of($combatant, 'Model_Combat_Actor'))
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
        foreach ($this->combatants as $combatant) {
            $this->scene->add_combatant($combatant);
            $combatant->enter();
        }


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

        foreach ($this->combatants as $combatant)
            $combatant->disengage();

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
        if (!count($this->combatants))
            return $this;

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

            if (!$combatant->position())
                $combatant->set_distance($t * $avg_distance, $jitter);
        }

        //Center
        $p1 = $this->field; $p2 = [0,0];
        foreach ($this->combatants as $combatant) {
            $p = $combatant->position();
            $p1 = [min($p[0], $p1[0]),min($p[1], $p1[1])];
            $p2 = [max($p[0], $p2[0]),max($p[1], $p2[1])];
        }

        $p_correct = [($this->field[0] - $p1[0] - $p2[0])/2, ($this->field[1] - $p1[1] - $p2[1])/2];
        foreach ($this->combatants as $combatant)
            $combatant->position($p_correct, true);

        return $this;
    }

    /**
     * @return int|null
     */
    public function get_winning_group() {
        if ($this->get_distinct_groups(true) !== 1) return null;

        foreach ($this->combatants as $combatant)
            if ($combatant->alive())
                return $combatant->group();

        return null;
    }

    /**
     * @param null|int $group
     * @param bool $instances
     * @return int
     */
    public function count_group_members($group = null, $instances = false) {
        $c = 0;

        foreach ($this->combatants as $combatant)
            if ($combatant->alive() && ($group === null || $combatant->group() == $group))
                $c += $instances ? 1 : $combatant->count();

        return $c;
    }


}