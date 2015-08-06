<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Combat_Actor {

    const MCA_TYPE_PLAYER = 1;
    const MCA_TYPE_ZOMBIE = 2;
    const MCA_TYPE_NPC = 3;
    const MCA_TYPE_PET = 4;

    /** @var  Model_Combat_Scene */
    protected $scene;

    protected $name;
    protected $type;
    protected $max_health;
    protected $health;
    protected $count;
    protected $group_id;
    protected $alive = true;
    protected $pos_x;
    protected $pos_y;

    protected $next_move = 100;

    protected $stat_initiative = 5;     // Each point increases speed by 5%
    protected $stat_damage = 5;         // Each point increases damage dealt by 5%
    protected $stat_resistance = 5;     // Each point reduces damage received by 5%
    protected $stat_accuracy = 5;       // Each point increases accuracy by 5%

    protected $movement_range = 5;

    protected $field = [64,40];
    protected $id;

    protected $ai_selfishness = 3;
    protected $ai_comradely = 0.5;
    protected $ai_volatile = 0.8;
    protected $ai_brashness = 0.4;


    /** @var Model_Combat_Weapon[] */
    protected $weapons = [];

    /** @var Model_Combat_Weapon */
    protected $current_weapon = null;

    /**
     * @return Model_Combat_Actor
     */
    public static function factory() {
        return new Model_Combat_Actor();
    }

    /**
     * @param Model_Combat_Scene $scene
     * @return Model_Combat_Actor
     */
    public function set_scene(&$scene) {
        $this->scene = $scene;
        return $this;
    }

    /**
     * @param null $ini
     * @param null $dmg
     * @param null $res
     * @param null $acc
     * @return int[]|Model_Combat_Actor
     */
    public function stats($ini = null, $dmg = null, $res = null, $acc = null) {
        if ($ini === null)
            return [$this->stat_initiative, $this->stat_damage, $this->stat_resistance, $this->stat_accuracy];
        else list($this->stat_initiative, $this->stat_damage, $this->stat_resistance, $this->stat_accuracy) = array_map(function($a) {return min(20,max(0,$a));}, [$ini, $dmg, $res, $acc]);
        $this->reset_steps();
        return $this;
    }

    /**
     * @param Model_Combat_Weapon|Model_Combat_Weapon[] $weapon
     * @return Model_Combat_Actor
     */
    public function add_weapon($weapon) {
        if (is_array($weapon))
            foreach ($weapon as $w)
                $this->add_weapon($w);
        else {
            if (!$this->current_weapon)
                $this->current_weapon = $weapon;
            $this->weapons[] = $weapon;
        }

        return $this;
    }

    /**
     * @return int[]
     */
    private function actual_stats() {
        return [
            round(100/(1 + $this->stat_initiative * 0.05)),
            1 + $this->stat_damage * 0.05,
            1 - $this->stat_resistance * 0.05,
            1 + $this->stat_accuracy * 0.05,
        ];
    }

    /**
     * @param int|null $new_group
     * @return int|Model_Combat_Actor
     */
    public function group($new_group = null) {
        if ($new_group === null) return $this->group_id;
        else $this->group_id = $new_group;
        return $this;
    }

    /**
     * @param string|null $new_name
     * @param int $type
     * @return Model_Combat_Actor|string
     */
    public function name($new_name = null, $type = 0) {
        if ($new_name === null) return $this->name;
        else {
            $this->name = $new_name;
            $this->type = $type;
        }
        return $this;
    }

    public function get_type() {
        return $this->type;
    }

    /**
     * @param int|null $health Current health
     * @param int|null $max_health
     * @param int $count
     * @return int[]|Model_Combat_Actor
     */
    public function strength($health = null, $max_health = null, $count = 1) {
        if ($health === null) return [$this->health, $this->max_health, $this->count];
        else {
            $this->health = $health;
            $this->max_health = ($max_health === null) ? $health : $max_health;
            $this->count = $count;
        }
        return $this;
    }

    /**
     * @param int|null $new_id
     * @return int|Model_Combat_Actor
     */
    public function id($new_id = null) {
        if ($new_id === null) return $this->id;
        else $this->id = $new_id;
        return $this;
    }

    /**
     * @param null|int[] $pos
     * @param bool|false $move
     * @return int[]|Model_Combat_Actor
     */
    public function position($pos = null, $move = false) {
        if ($pos === null) return [$this->pos_x,$this->pos_y];
        elseif ($move) {
            $this->pos_x += $pos[0];
            $this->pos_y += $pos[1];
        } else list($this->pos_x, $this->pos_y) = $pos;

        $this->pos_x = max(0,min($this->field[0], $this->pos_x));
        $this->pos_y = max(0,min($this->field[1], $this->pos_y));

        return $this;
    }

    /**
     * @return bool
     */
    public function alive() {
        return $this->alive;
    }

    /**
     * @param null|int $dif
     * @return Model_Combat_Actor|int
     */
    public function next_step_counter($dif = null) {
        if ($dif === null)
            return $this->next_move;
        else $this->next_move += $dif;
        return $this;
    }

    private function reset_steps() {
        list($this->next_move) = $this->actual_stats();
    }

    /**
     * @param Model_Combat_Actor $combatant
     * @return float
     */
    public function distance_from($combatant) {
        return sqrt(pow($this->pos_x - $combatant->pos_x, 2) + pow($this->pos_y - $combatant->pos_y, 2));
    }

    /**
     * @param $data
     * @return null|array
     */
    private function get_recommendation($data) {
        if (!$data) return null;
        usort($data, function($a,$b) {return $a[0] - $b[0];});
        return $data[0];
    }

    /**
     * @param Model_Combat_Actor[] $friends
     * @param Model_Combat_Actor[] $foes
     * @param Model_Combat_Weapon|null $weapon
     * @param bool $ignore_range
     * @return array|null
     */
    protected function get_attack_priority($friends, $foes, $weapon = null, $ignore_range = false) {
        if ($weapon === null)
            $weapon = $this->current_weapon;

        if (!$weapon) return null;

        $ret = null;
        foreach ($foes as $foe) {
            $priority = max(1,$foe->can_attack($this) ? $this->ai_selfishness : 1);
            foreach ($friends as $friend)
                $priority += ($foe->can_attack($friend) ? $this->ai_comradely : 0);

            $p = $weapon->potential_damage($this, $foe, $ignore_range, $this->count) * $priority;
            if ($p <= 0) continue;

            if (!$ret || $ret[0] < $p)
                $ret = [
                    $weapon->potential_damage($this, $foe, $ignore_range, $this->count) * $priority,
                    $foe,
                    $weapon,
                ];
        }

        return $ret;
    }

    /**
     * @param Model_Combat_Actor[] $friends
     * @param Model_Combat_Actor[] $foes
     * @return array|null
     */
    protected function get_weapon_priority($friends, $foes) {
        $tmp = [];
        foreach ($this->weapons as $weapon)
            $tmp[] = $this->get_attack_priority($friends, $foes, $weapon);

        $rec = $this->get_recommendation($tmp);
        /** @noinspection PhpUndefinedMethodInspection */
        if (!$rec || ($this->current_weapon && $rec[2]->uin() == $this->current_weapon->uin())) return null;
        else return [
            $rec[0] * $this->ai_volatile,
            $rec[2]
        ];
    }

    /**
     * @param Model_Combat_Actor[] $friends
     * @param Model_Combat_Actor[] $foes
     * @return array|null
     */
    protected function get_movement_priority($friends, $foes) {
        if ($this->current_weapon && ($closest_foe = $this->current_weapon->closest_foe($this, $foes))) {
            $tmp = $this->get_attack_priority($friends, [$closest_foe], null, true);
            return $tmp ? [
                $tmp[0] * $this->ai_brashness,
                $tmp[1]
            ] : [];
        } return [];
    }

    protected function can_attack($combatant) {
        if (!$this->current_weapon) return false;
        else return $this->current_weapon->in_range($this, $combatant);
    }

    protected function damage($damage) {
        if ($damage < $this->health) {
            $kills = 0;
            $death = false;
            $this->health -= $damage;
        } elseif ($damage < ($this->health + ($this->count - 1) * $this->max_health)) {
            $kills = floor(($damage - $this->health)/$this->max_health);
            $death = false;
            $this->health = $this->max_health - (($damage - $this->health) - $this->max_health * $kills);
            $this->count -= $kills;
        } else {
            $damage = $this->health + ($this->count - 1) * $this->max_health;
            $kills = $this->count;
            $death = true;
            $this->alive = false;
            $this->health = 0;
            $this->count = 0;
        }

        $this->scene->damage($this, $damage, $kills, $death);
    }

    /**
     * @param Model_Combat_Actor[] $friends
     * @param Model_Combat_Actor[] $foes
     * @param bool $second_act
     */
    public function act($friends, $foes, $second_act = false) {
        if (!$second_act)
            $this->reset_steps();

        $attack = $this->get_attack_priority($friends, $foes);
        $switch = $second_act ? [] : $this->get_weapon_priority($friends, $foes);
        $move = $second_act ? [] : $this->get_movement_priority($friends, $foes);

        $this->scene->dbg_battle_ai($this, $attack, $switch, $move);

        list($ini, $atk, $res, $acc) = $this->actual_stats();
        $use_second_action = false;

        if ($attack && (!$switch || $attack[0] > $switch[0]) && (!$move || $attack[0] > $move[0])) {
            // Attack action
            /** @var Model_Combat_Actor $target */
            $target = $attack[1];
            list($op_ini, $op_atk, $op_res, $op_acc) = $this->actual_stats();

            $dmg = $this->current_weapon->calculate_damage($this, $target, $this->count, $acc, $atk, $op_res);
            $this->scene->attack($this, $target, $this->current_weapon);
            $target->damage($dmg);
            $this->current_weapon->trigger_usage($this, $target, $dmg, $this->scene);

        } elseif ($switch && (!$attack || $switch[0] > $attack[0]) && (!$move || $switch[0] > $move[0])) {
            // Switch action
            $this->current_weapon = $switch[1];
            $this->scene->switch_weapon($this, $this->current_weapon);

        } elseif ($move && (!$switch || $move[0] > $switch[0]) && (!$attack || $move[0] > $attack[0])) {
            // Move action
            /** @var Model_Combat_Actor $target */
            $target = $move[1];

            $d = $this->distance_from($target);
            $d_min = $d - $this->current_weapon->max_range();

            $old_x = $this->pos_x;
            $old_y = $this->pos_y;

            $dx = ($target->pos_x - $this->pos_x)/$d;
            $dy = ($target->pos_y - $this->pos_y)/$d;

            if ($d_min <= $this->movement_range) {
                $this->pos_x += $dx * $d_min;
                $this->pos_y += $dy * $d_min;
                $use_second_action = ($d_min < $this->movement_range);
            } else {
                $this->pos_x += $dx * $this->movement_range;
                $this->pos_y += $dy * $this->movement_range;
            }

            $this->scene->move($this, [$this->pos_x, $this->pos_y], sqrt(pow($old_x - $this->pos_x, 2) + pow($old_y - $this->pos_y, 2)), $target);
        } else {
            // Idle action
        }

        if ($use_second_action) $this->act($friends, $foes, true);

    }

}