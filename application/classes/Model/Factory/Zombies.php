<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Factory_Zombies extends Model_Factory_Abstract {

    protected static $base = 'zombies';
    protected static $expected_result_class = 'Model_Combat_Actor';

    private $strength = 0;
    private $strength_factor = 1;
    private $max_adversaries = 1;

    private $chance = 0.1;
    private $block = 0.5;

    private $range = [10,30];

    private $accumulation = 0;
    private $last_decay = 0;

    /**
     * @param $str
     * @param $max
     * @return Model_Factory_Zombies
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

    public function get_strength_factor() {
        /** @global Model_Game $game */
        global $game;

        $s = $this->strength_factor;
        $since = $game->duration() - $this->last_decay;

        return max(0,min(1,$s + $since * 0.0007));
    }

    public function reduce_strangth_factor($by) {
        /** @global Model_Game $game */
        global $game;

        $this->strength_factor = $this->get_strength_factor();
        $this->last_decay = $game->duration();

        $this->strength_factor -= $this->strength_factor * $by;
    }

    /**
     * @param $min
     * @param $max
     * @return Model_Factory_Zombies
     */
    public function set_range($min, $max) {
        $this->range = [$min, $max];
        return $this;
    }

    protected function get_game_strength() {
        /** @global Model_Game $game */
        global $game;

        return 1 + max(0, ($game->duration()/2016) - 1) * 0.3;
    }

    public function get_strength($include_factor = true) {

        return $this->strength * ($include_factor ? ($this->get_game_strength() * $this->get_strength_factor()) : 1);
    }

    public function get_max_group_count() {
        return $this->max_adversaries;
    }


    /**
     * @param null|int $set
     * @return Model_Factory_Zombies|int
     */
    public function accumulation($set = null) {
        if ($set === null)
            return $this->accumulation;
        else {
            $this->accumulation = $set;
            return $this;
        }
    }

    /** @deprecated */
    public function get_radar_data() {
        $min_cl = null;
        foreach ($this->get() as $zcl => $c)
            /** @var Model_Combat_Zombies_Zombie $zcl */
            if ($min_cl === null || $min_cl > $zcl::get_strength_quantifier())
                $min_cl = $zcl::get_strength_quantifier();
        $min_cl = $min_cl > 0 ? floor($this->strength/$min_cl) : 0;

        return [1, $min_cl, $this->chance * (1 - $this->block), $this->chance * $this->block];
    }

    public function release() {
        return $this->spawn(true, false, 1, $this->accumulation);
    }

    public function dry_spawn($force = false) {
        if (!$this->max_adversaries || !$this->get_strength() || (!$force && (mt_rand()/mt_getrandmax()) < $this->chance))
            return;

        if ((mt_rand()/mt_getrandmax()) < $this->block)
            $this->accumulation++;
    }

    /**
     * @param bool|false $force
     * @param bool|true $apply_decay
     * @param int $strength_modifier
     * @param null|int $fixed_number
     * @return Model_Combat_Zombies_Zombie[]|null
     */
    public function spawn($force = false, $apply_decay = true, $strength_modifier = 1, $fixed_number = null) {
        if ($fixed_number === 0 || $fixed_number < 0 || !$this->max_adversaries || !($str = $this->get_strength() * $strength_modifier) || (!$force && (mt_rand()/mt_getrandmax()) > $this->chance))
            return null;

        if (!$force && !$fixed_number && (mt_rand()/mt_getrandmax()) < $this->block) {
            $this->accumulation++;
            return null;
        }

        $army = [];
        for ($i = 0; $i < $this->max_adversaries; $i++) {
            /** @var Model_Combat_Zombies_Zombie $tmp */
            $tmp = $this->get_element();
            $army[] = $tmp;
        }

        if ($force && !$army)
            $army = [Model_Combat_Zombies_Shambler::cls()];
        elseif (!$army) return null;

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

        if (!$accum_count && $force) {
            $accum_count = 1;
            $accum_army = [['count' => 1, 'class' => Model_Combat_Zombies_Shambler::cls()]];
        } elseif (!$accum_count)
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
                $ret[] = $z::factory()->count($entry['count'])->set_distance(mt_rand($this->range[0], $this->range[1]), 0);
            }

        if ($apply_decay)
            $this->reduce_strangth_factor(($str - $accum_str)/(6 * $str));

        return $ret;
    }
}	