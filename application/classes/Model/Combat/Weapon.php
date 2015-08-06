<?php defined('SYSPATH') OR die('No direct access allowed.');

abstract class Model_Combat_Weapon extends Model_Items_Abstract_Item {

    protected static $damage = [1,1];
    protected static $range = [0,PHP_INT_MAX];
    protected static $accuracy = 1;
    protected static $use_fixed_accuracy = true;
    protected static $aoe = false;
    protected static $friendly_fire = false;

    /** @var Model_Player */
    protected $registered_user;

    public function usable() {
        return true;
    }

    protected function damage() {
        return static::$damage;
    }

    protected function accuracy() {
        return static::$accuracy;
    }

    public function get_ammo_icons() {
        return [];
    }

    protected function aoe() {
        return static::$aoe;
    }

    protected function friendly_fire() {
        return static::$friendly_fire;
    }

    protected function range() {
        return static::$range;
    }

    public function max_range() {
        return static::$range[1];
    }

    /**
     * @param Model_Player $p
     */
    public function register($p) {
        $this->registered_user = $p;
    }

    /**
     * @param $distance
     * @param int $modifier
     * @return float
     */
    private function get_accuracy($distance, $modifier = 1) {
        $distance = round($distance, 2);

        if ($distance < $this->range()[0] || $distance > $this->range()[1]) return 0;
        elseif (static::$use_fixed_accuracy) $tmp = min(1,max(0,$this->accuracy()));
        else $tmp = min(1,max(0,(1 - ($distance - $this->range()[0])/($this->range()[1] - $this->range()[0])) * $this->accuracy()));

        if ($tmp == 1 || $tmp == 0 || $modifier == 1) return $tmp;
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
    public function potential_damage($me, $other, $ignore_range = false, $count = 1) {
        if (!$this->usable())
            return 0;
        else {
            list($oh, $ohm, $c) = $other->strength();
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
    public function closest_foe($me, $others, $include_in_range = true) {
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
    public function in_range($me, $foes) {
        if (is_array($foes))
            return array_filter($foes, function($c) use ($me) {
                return $this->get_accuracy($me->distance_from($c)) > 0;
            });
        else return $this->get_accuracy($me->distance_from($foes)) > 0;
    }

    /**
     * @param Model_Combat_Actor $me
     * @param Model_Combat_Actor $opponent
     * @param int $multiply
     * @param int $accuracy
     * @param int $atk
     * @param int $res
     * @return bool|number
     */
    public function calculate_damage($me, $opponent, $multiply = 1, $accuracy = 1, $atk = 1, $res = 1) {
        if (!$this->usable()) return false;

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

        $raw = (($actual_multiply <= 0 ? 0 : mt_rand($this->damage()[0] * $actual_multiply, $this->damage()[1] * $actual_multiply)) * $atk) / $res;
        if (!$this->aoe())
            $raw = min($raw, $opponent->strength()[0]);
        return $raw;
    }

    /**
     * @param Model_Combat_Actor $me
     * @param Model_Combat_Actor $opponent
     * @param number $damage
     * @param Model_Combat_Scene $scene
     * @return bool
     */
    public function trigger_usage($me, $opponent, $damage, $scene) {
        return true;
    }

}