<?php defined('SYSPATH') OR die('No direct access allowed.');

abstract class Model_Combat_Weapon extends Model_Items_Abstract_Equipable {

    public const MCW_ANIMATION_NONE = 0;
    public const MCW_ANIMATION_PUNCH = 1;
    public const MCW_ANIMATION_SLASH = 2;
    public const MCW_ANIMATION_SHOT_BAT = 3;
    public const MCW_ANIMATION_SHOT_AMMO = 4;
    public const MCW_ANIMATION_SHOT_WATER = 5;
    public const MCW_ANIMATION_THROW = 6;
    public const MCW_ANIMATION_SLASH_MULTI = 7;
    public const MCW_ANIMATION_SHOT_ENERGY = 8;
    public const MCW_ANIMATION_ZOMBIE_MUNCH = 9;
    public const MCW_ANIMATION_CHAINSAW = 10;
    public const MCW_ANIMATION_SHOT_BOLT = 11;
    public const MCW_ANIMATION_SHOT_SPLINTER = 12;
    public const MCW_ANIMATION_SHOT_RLASER = 13;

    protected static $equipment_type = Model_Items_Abstract_Equipable::MIAE_WEAPON;

    protected static $allow_multi_equip = true;
    protected static $allow_primary_equip = true;

    protected static $damage = [1,1];
    protected static $range = [0,PHP_INT_MAX];
    protected static $accuracy = 1;
    protected static $use_fixed_accuracy = true;
    protected static $accuracy_downscale = 0;
    protected static $aoe = false;
    protected static $friendly_fire = false;
    protected static $durabillity = 1;

    protected static $animation = Model_Combat_Weapon::MCW_ANIMATION_NONE;
    protected $ignore_equip = false;

    protected $broken = false;

    /** @var Interface_Plentity|Model_Player */
    protected $registered_user;

    public function ignore_equip(): void {
        $this->ignore_equip = true;
    }

    public function usable(): bool {
        return $this->is_equipped() || $this->ignore_equip;
    }

    public function get_animation(): int {
        return static::$animation;
    }

    public function durabillity(): int {
        return static::$durabillity;
    }

    protected function damage(): array {
        return static::$damage;
    }

    public function accuracy(): float {
        return static::$accuracy;
    }

    public function fixed_accuracy(): float {
        return static::$use_fixed_accuracy;
    }

    public function get_ammo_icons(): array {
        return [];
    }

    protected function aoe(): bool {
        return static::$aoe;
    }

    protected function friendly_fire(): bool {
        return static::$friendly_fire;
    }

    protected function range(): array {
        return static::$range;
    }

    public function min_range(): float {
        return $this->range()[0];
    }

    public function max_range(): float {
        return $this->range()[1];
    }

    public function generate_wound(/** @noinspection PhpUnusedParameterInspection */
        $damage): ?string {
        return null;
    }

    /**
     * @param Interface_Plentity $p
     * @return Model_Combat_Weapon
     */
    public function register($p): self {
        $this->registered_user = $p;
        return $this;
    }

    /**
     * @return Model_Combat_Weapon
     */
    public function unregister(): self {
        $this->registered_user = null;
        return $this;
    }

    protected function accuracy_downscale(): int {
        return static::$accuracy_downscale;
    }

    /**
     * @param $distance
     * @param int $modifier
     * @return float
     */
    private function get_accuracy($distance, $modifier = 1): float {
        $distance = round($distance, 2);

        if ($distance < $this->range()[0] || $distance > $this->range()[1]) return 0;
        elseif (static::$use_fixed_accuracy) $tmp = min(1,max(0,$this->accuracy()));
        else $tmp = min(1,max(0,$this->accuracy_downscale() + (1 - ($distance - $this->range()[0])/($this->range()[1] - $this->range()[0])) * ($this->accuracy() - $this->accuracy_downscale())));

        if ($tmp === 1 || $tmp === 0 || $modifier === 1) return $tmp;
        elseif ($modifier < 1) return $tmp * $modifier;
        else return 1 - (1 - $tmp)/$modifier;
    }

    /**
     * @param Model_Combat_Actor $me
     * @param Model_Combat_Actor $other
     * @param bool $ignore_range
     * @param int $count
     * @return float|int
     */
    public function potential_damage($me = null, $other = null, $ignore_range = false, $count = 1) {
        if (!$me || !$other)
            return 0;

        if (!$this->usable())
            return 0;
        else {
            [$oh, $ohm, $c] = $other->strength();
            $max_damage = $this->aoe() ? ($oh + ($ohm * ($c - 1))) : $oh;
            return min(
                ($this->damage()[0] * $count + $this->damage()[1] * $count)/2 * ($ignore_range ? 1 : $this->get_accuracy($other->distance_from($me)))
                , $max_damage);
        }
    }

    /**
     * @param Model_Combat_Actor $me
     * @param Model_Combat_Actor[] $others
     * @param bool $include_in_range
     * @return Model_Combat_Actor
     */
    public function closest_foe(Model_Combat_Actor $me, $others, $include_in_range = true): Model_Combat_Actor {
        $a = PHP_INT_MAX;
        $ret = null;
        foreach ($others as $other)
            if ($include_in_range || !$this->in_range($me, $other)) {
                if (($d = $other->distance_from($me)) < $a) {
                    $a = $d;
                    $ret = $other;
                }
            }
        return $ret;
    }

    /**
     * @param Model_Combat_Actor $me
     * @param Model_Combat_Actor[]|Model_Combat_Actor $foes
     * @return Model_Combat_Actor[]|bool
     */
    public function in_range(Model_Combat_Actor $me, $foes) {
        if (is_array($foes))
            return array_filter($foes, function($c) use ($me) {
                return $this->get_accuracy($me->distance_from($c)) > 0;
            });
        else return $this->get_accuracy($me->distance_from($foes)) > 0;
    }

    /**
     * @param Model_Combat_Actor $me
     * @param Model_Combat_Actor $opponent
     * @param int                $multiply
     * @param float              $accuracy
     * @param int                $atk
     * @param int                $res
     *
     * @return number[]
     * @throws Exception
     */
    public function calculate_damage(Model_Combat_Actor $me, $opponent, $multiply = 1, $accuracy = 1.0, $atk = 1, $res = 1): array {
        if (!$this->usable()) return [0,0];

        $accuracy = $this->get_accuracy($opponent->distance_from($me), $accuracy);
        if ($accuracy <= 0)
            $actual_multiply = 0;
        elseif ($accuracy >= 1)
            $actual_multiply = $multiply;
        else {
            $actual_multiply = $multiply;
            for ($i = 0; $i < $multiply; $i++)
                if (mt_rand()/mt_getrandmax() > $accuracy) $actual_multiply--;
        }

        $raw = ($actual_multiply <= 0 ? 0 : random_int($this->damage()[0] * $actual_multiply, $this->damage()[1] * $actual_multiply));
        if (!$this->aoe())
            $raw = min($raw, $opponent->strength()[0]);
        return [$raw * (1 + ($atk - $res)), $raw * $atk];
    }

    /**
     * @param Model_Combat_Actor $me
     * @param Model_Combat_Actor $opponent
     * @param number $damage
     * @param Model_Combat_Scene $scene
     * @return bool
     */
    public function trigger_usage(Model_Combat_Actor $me, Model_Combat_Actor $opponent, $damage, Model_Combat_Scene $scene): bool {
        $d = $this->durabillity();
        if ($d < 1 && mt_rand()/mt_getrandmax() > $d)
            $this->weapon_break($me, $opponent, $damage, $scene);
        return true;
    }

    /**
     * @param Model_Combat_Actor $me
     * @param Model_Combat_Actor $opponent
     * @param number $damage
     * @param Model_Combat_Scene $scene
     */
    protected function weapon_break(Model_Combat_Actor $me, Model_Combat_Actor $opponent, $damage, Model_Combat_Scene $scene): void {
        $this->broken = true;
        $scene->break_weapon($me, $this);
    }

}