<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Combat_Actor extends Named {

    const MCA_TYPE_PLAYER = 1;
    const MCA_TYPE_ZOMBIE = 2;
    const MCA_TYPE_NPC = 3;
    const MCA_TYPE_PET = 4;

    const MCA_STAT_INI = 1;
    const MCA_STAT_ATK = 2;
    const MCA_STAT_DEF = 3;
    const MCA_STAT_ACC = 4;

    /** @var  Model_Combat_Scene */
    protected $scene;

    protected static $custom_sprite = null;
    protected static $custom_death_sprite = null;

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
    protected $ai_brashness = 0.7;

    protected static $is_unique = true;

    protected $wounds = [];

    /** @var Model_Combat_Weapon[] */
    protected $weapons = [];

    /** @var  Model_Inventory */
    protected $inventory;

    /** @var Model_Items_Abstract_Armor[] */
    protected $armor = [];

    /** @var Model_Combat_Weapon */
    protected $current_weapon = null;

    /**
     * @return Model_Combat_Actor
     */
    public static function factory() {
        $s = get_called_class();
        return new $s;
    }

    public function unique() {
        return static::$is_unique;
    }

    public function get_avatar() {
        return null;
    }

    public function customSprite($death_sprite = false) {
        return $death_sprite ? static::$custom_death_sprite : static::$custom_sprite;
    }

    public function __construct() {
        $this->health = $this->max_health;
    }

    /**
     * @param Model_Buffs_Abstract_Buff|null $wound
     */
    public function inflict_wound($wound) {
        if ($wound) {
            $this->scene->injury($this, $wound::static_name(), $wound::static_icon());
            $this->wounds[] = $wound;
        }
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
     * @param Model_Inventory $inv
     * @return Model_Combat_Actor
     */
    public function register_inventory($inv, $check_equip = true) {
        $this->inventory = $inv;

        foreach ($this->inventory->get('Model_Combat_Weapon') as $w)
            /** @var Model_Combat_Weapon $w */
            if (!$check_equip || $w->is_equipped())
                $this->add_weapon($w);

        foreach ($this->inventory->get('Model_Items_Abstract_Armor') as $a)
            /** @var Model_Items_Abstract_Armor $a */
            if (!$check_equip || $a->is_equipped())
                $this->add_armor($a);

        return $this;
    }

    /**
     * @return Model_Inventory
     */
    public function inventory() {
        return $this->inventory;
    }

    /**
     * @param null $ini
     * @param null $dmg
     * @param null $res
     * @param null $acc
     * @return int[]|Model_Combat_Actor
     */
    public function stats($ini = null, $dmg = null, $res = null, $acc = null) {
        if ($ini === null) {
            $tmp = [0,0,0,0];
            foreach ($this->armor as $armor)
                if (!$armor->is_destroyed())
                    foreach ($armor->get_stats() as $id => $v)
                        $tmp[$id] += $v;

            if ($this->current_weapon && $this->current_weapon->usable())
                foreach ($this->current_weapon->get_stats() as $id => $v)
                    $tmp[$id] += $v;

            return [$this->stat_initiative + $tmp[0], $this->stat_damage + $tmp[1], $this->stat_resistance + $tmp[2], $this->stat_accuracy + $tmp[3]];
        }

        else list($this->stat_initiative, $this->stat_damage, $this->stat_resistance, $this->stat_accuracy) = array_map(function($a) {return min(20,max(0,$a));}, [$ini, $dmg, $res, $acc]);
        $this->reset_steps();
        return $this;
    }

    /**
     * @param $distance
     * @param int $jitter
     * @return Model_Combat_Actor
     */
    public function set_distance($distance, $jitter = 3) {
        $this->position([$distance + mt_rand(-$jitter, $jitter), 12 + mt_rand(0, $this->field[1] - 12)]);
        return $this;
    }

    /**
     * @param Model_Combat_Weapon|Model_Combat_Weapon[] $weapon
     * @return Model_Combat_Actor
     */
    public function add_weapon($weapon) {
        /** @global Model_Game $game */
        global $game;
        if (is_array($weapon))
            foreach ($weapon as $w)
                $this->add_weapon($w);
        else {
            if (!$weapon->uin()) $game->uin()->set($weapon);
            if (!$this->current_weapon || $weapon->is_equipped_primary())
                $this->current_weapon = $weapon;
            $this->weapons[$weapon->uin()] = $weapon;
        }

        return $this;
    }

    /**
     * @param Model_Items_Abstract_Armor|Model_Items_Abstract_Armor[] $armor
     * @return Model_Combat_Actor
     */
    public function add_armor($armor) {
        if (is_array($armor))
            foreach ($armor as $a)
                $this->add_armor($a);
        else {
            if (!$armor->is_equipped()) return $this;
            $this->armor[] = $armor;
        }

        return $this;
    }

    /**
     * @return int[]
     */
    protected function actual_stats() {
        list($ini, $atk, $def, $acc) = $this->stats();
        return [
            round(100/(1 + $ini * 0.05)),
            1 + $atk * 0.05,
            1 - $def * 0.05,
            1 + $acc * 0.05,
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
    public function name($new_name = null, $type = null) {
        if ($new_name === null) return $this->name;
        else {
            $this->name = $new_name;
            if ($type !== null) $this->type = $type;
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
     * @param null|int $new
     * @return Model_Combat_Actor
     */
    public function count($new = null) {
        if ($new === null) return $this->count;
        else $this->count = $new;
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
     * @return null|int[]|Model_Combat_Actor
     */
    public function position($pos = null, $move = false) {
        if ($pos === null) return ($this->pos_x === null || $this->pos_y === null) ? null : [$this->pos_x,$this->pos_y];
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
    public function distance_from(Model_Combat_Actor $combatant) {
        return sqrt(pow($this->pos_x - $combatant->pos_x, 2) + pow($this->pos_y - $combatant->pos_y, 2));
    }

    protected function rounds_to_use(Model_Combat_Weapon $weapon, $foe) {
        if (is_array($foe))
            return min(array_map(function($a) use ($weapon) {return $this->rounds_to_use($weapon, $a);}, $foe));
        else {
            if ($this->movement_range <= 0) return PHP_INT_MAX;

            if ($weapon->in_range($this, $foe))
                return 0;

            $min = $weapon->min_range();
            $max = $weapon->max_range();
            $dist = $this->distance_from($foe);

            return ceil(($dist < $min ? ($min - $dist) : ($dist - $max))/$this->movement_range);
        }
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
                if ($friend->id() != $this->id())
                    $priority += ($foe->can_attack($friend) ? $this->ai_comradely : 0);

            $p = $weapon->potential_damage($this, $foe, $ignore_range, $this->count) * $priority;
            if ($p <= 0) continue;

            if (!$ret || $ret[0] < $p)
                $ret = [
                    $p,
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
        $tmp = null;

        $current_rounds = ($this->current_weapon && $this->current_weapon->usable()) ? $this->rounds_to_use($this->current_weapon, $foes) : -1;

        foreach ($this->weapons as $weapon) {
            $foe = $weapon->closest_foe($this, $foes, true);
            $res = $this->get_attack_priority($friends, [$foe], $weapon, true);
            if (!$res) continue;
            if (!$weapon->in_range($this, $foe)) {

                if ($current_rounds == 0) continue;
                $rounds = $this->rounds_to_use($weapon, $foe);
                if ($rounds > 1 && $this->distance_from($foe) < $weapon->min_range()) continue;

                $factor = max(0, min(1,$current_rounds < 0 ? 1 : ($current_rounds/$rounds)));
                $res[0] = ($rounds > 1 && $this->distance_from($foe) < $weapon->min_range()) ? 0 : ($res[0] * $this->ai_brashness * $factor);
            }

            if (!$tmp || $tmp[0] < $res[0])
                $tmp = $res;
        }

        /** @noinspection PhpUndefinedMethodInspection */
        if (!$tmp || ($this->current_weapon && $this->current_weapon->usable() && ($this->current_weapon->uin() == $tmp[2]->uin() || get_class($this->current_weapon) == get_class($tmp[2])))) return null;
        else return [
            $tmp[0] * $this->ai_volatile,
            $tmp[2]
        ];
    }

    /**
     * @param Model_Combat_Actor[] $friends
     * @param Model_Combat_Actor[] $foes
     * @return array|null
     */
    protected function get_movement_priority($friends, $foes) {
        if ($this->movement_range <= 0) return [];
        if ($this->current_weapon && ($closest_foe = $this->current_weapon->closest_foe($this, $foes, false))) {
            $tmp = $this->get_attack_priority($friends, [$closest_foe], $this->current_weapon, true);

            return $tmp ? [
                ($tmp[0] * $this->ai_brashness)/$this->rounds_to_use($this->current_weapon, $closest_foe),
                $tmp[1],
                $closest_foe->distance_from($this) < $this->current_weapon->min_range() ? -1 : 1
            ] : [];
        } return [];
    }

    protected function can_attack(Model_Combat_Actor $combatant) {
        if (!$this->current_weapon) return false;
        else return $this->current_weapon->in_range($this, $combatant);
    }

    /**
     * @param int $damage
     * @param int $kills
     * @param int $death
     * @param Model_Combat_Actor $target
     */
    protected function score_kills($damage, $kills, $death, $target) {}

    /**
     * @param int $damage
     * @param null|Model_Combat_Actor $from
     * @param null|int $armor_damage
     */
    protected function damage($damage, $from = null, $armor_damage = null) {
        $this->health -= $damage;

        $kills = min($this->count, ($this->health <= 0 ? (-floor(abs($this->health) / $this->max_health) + 1) : 0));
        $this->alive = $kills < $this->count;

        if ($kills) {
            $this->health = !$this->alive ? 0 : ($this->health + $kills * $this->max_health);
            $this->count = !$this->alive ? 0 : ($this->count - $kills);
        }

        if ($from)
            $from->score_kills($damage, $kills, !$this->alive(), $this);

        $this->scene->damage($this, $damage, $kills, !$this->alive);

        if ($armor_damage) {
            $list = [];
            foreach ($this->armor as $proto)
                if (!$proto->is_destroyed())
                    $list[] = $proto;

            if (count($list)) {
                /** @var Model_Items_Abstract_Armor $armor */
                $armor = Tool_Gambling::select($list);
                $armor->take_damage($armor_damage);
                if ($armor->is_destroyed())
                    $this->scene->break_armor($this, $armor);
            }
        }
    }

    public function enter() {}

    public function idle() {}

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

            list($dmg,$dmg_raw) = $this->current_weapon->calculate_damage($this, $target, $this->count, $acc, $atk, $op_res);
            $this->scene->attack($this, $target, $this->current_weapon, $dmg);
            $target->damage($dmg, $this, $dmg_raw);
            $target->inflict_wound($this->current_weapon->generate_wound($dmg));
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
            $d_min = $d - ($move[2] > 0 ? $this->current_weapon->max_range() : $this->current_weapon->min_range());

            $old_x = $this->pos_x;
            $old_y = $this->pos_y;

            $dx = ($target->pos_x - $this->pos_x)/$d;
            $dy = ($target->pos_y - $this->pos_y)/$d;

            if (abs($d_min) <= $this->movement_range) {
                $this->pos_x += $dx * $d_min;
                $this->pos_y += $dy * $d_min;
                $use_second_action = (abs($d_min) < $this->movement_range);
            } else {
                $this->pos_x += $dx * $this->movement_range * $move[2];
                $this->pos_y += $dy * $this->movement_range * $move[2];
            }

            $this->pos_x = max(0,min($this->field[0], $this->pos_x));
            $this->pos_y = max(0,min($this->field[1], $this->pos_y));

            $dist = sqrt(pow($old_x - $this->pos_x, 2) + pow($old_y - $this->pos_y, 2));
            $this->scene->move($this, [$this->pos_x, $this->pos_y], $dist, $target);
        } else {
            // Idle action
            $this->idle();
        }

        if ($use_second_action) $this->act($friends, $foes, true);

    }

    public function disengage() {
        return true;
    }

}