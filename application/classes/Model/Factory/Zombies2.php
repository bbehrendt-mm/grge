<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Factory_Zombies2 extends Model_Factory_Abstract {

    protected static $base = 'zombies';

    private $strength = 0;
    private $strength_factor = 1;
    private $max_adversaries = 1;

    private $chance = 0.1;
    private $block = 0.5;

    private $range = [10,30];

    private $accumulation = 0;

    /**
     * @param $str
     * @param $max
     * @return Model_Factory_Zombies2
     */
    public function set_strength($str, $max) {
        $this->strength = $str;
        $this->max_adversaries = $max;

        return $this;
    }

    /**
     * @param $encounter_rate
     * @param float $block_rate
     * @return $this
     */
    public function set_chance($encounter_rate, $block_rate = 0.5) {
        $this->chance = $encounter_rate;
        $this->block = $block_rate;
        return $this;
    }

    /**
     * @param $min
     * @param $max
     * @return Model_Factory_Zombies2
     */
    public function set_range($min, $max) {
        $this->range = [$min, $max];
        return $this;
    }

    public function get_strength($include_factor = true) {
        return $this->strength * ($include_factor ? max(0,min(1,$this->strength_factor)) : 1);
    }

    /**
     * @param null|int $set
     * @return Model_Factory_Zombies2|int
     */
    public function accumulation($set = null) {
        if ($set === null)
            return $this->accumulation;
        else {
            $this->accumulation = $set;
            return $this;
        }
    }

    /**
     * @param bool|false $force
     * @param bool|true $apply_decay
     * @param int $strength_modifier
     * @param null|int $fixed_number
     * @return Model_Combat_Zombies_Zombie[]|null
     */
    public function spawn($force = false, $apply_decay = true, $strength_modifier = 1, $fixed_number = null) {
        if (!$this->max_adversaries || !($str = $this->get_strength() * $strength_modifier) || (!$force && (mt_rand()/mt_getrandmax()) < $this->chance))
            return null;

        if (!$force && !$fixed_number && (mt_rand()/mt_getrandmax()) < $this->block) {
            $this->accumulation++;
            return null;
        }

        $army = [];
        for ($i = 0; $i < $this->max_adversaries; $i++) {
            $tmp = $this->get_element();
            if ($tmp) $army[] = $tmp;
        }

        if (!$army) return null;
        usort($army, function($a, $b) {
           /**
            * @var Model_Combat_Zombies_Zombie $a
            * @var Model_Combat_Zombies_Zombie $b
            */
            return $b::get_strength_quantifier() - $a::get_strength_quantifier();
        });

        $accum_count = 0;
        $accum_str = $str;
        $accum_army = [];
        foreach ($army as $zclass) {
            /** @var Model_Combat_Zombies_Zombie $zclass */
            if (!($max_num = floor($accum_str/$zclass::get_strength_quantifier())))
                continue;
            $accum_count += ($num = mt_rand(1, $max_num));
            $accum_str -= $num * $zclass::get_strength_quantifier();

            $accum_army[] = ['count' => $num, 'class' => $zclass];
        }

        if (!$accum_count)
            return null;

        if ($fixed_number && $accum_count != $fixed_number) {

            if ($accum_count < $fixed_number) {
                $f = $fixed_number/$accum_count;

                $accum_count = 0;
                foreach ($accum_army as &$entry)
                    $accum_count += ($entry['count'] = ceil($entry['count'] * $f));
            }

            $i = 0;
            while ($accum_count > $fixed_number) {
                if ($i >= count($accum_army))
                    $i = 0;

                if ($accum_army[$i]['count']) {
                    $accum_army[$i]['count']--;
                    $accum_count--;
                }

                $i++;
            }

        }

        $ret = [];
        foreach ($accum_army as $entry)
            if ($entry['count'] > 0) {
                /** @var Model_Combat_Zombies_Zombie $z */
                $z = $entry['class'];
                $ret[] = $z::factory()->count($entry['count'])->set_distance(mt_rand($this->range[0], $this->range[1]));
            }

        if ($apply_decay)
            $this->strength_factor -= $this->strength_factor * (($str - $accum_str)/(6 * $str));

        return $ret;
    }
}	