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

    private $accumulated_zombies = [];

    private $last_decay = 0;

    private $hideout_mode = false;

    public function enable_hideout_mode(): void {
        $this->hideout_mode = true;
    }

    /**
     * @param $str
     * @param $max
     * @return Model_Factory_Zombies
     */
    public function set_strength($str, $max): \Model_Factory_Zombies
    {
        $this->strength = $str;
        $this->max_adversaries = $max;

        $game = Globals::CurrentGame();
        $factor = $game ? $game->config('zombies.power') : 1.0;

        if ($factor > 0) $this->strength *= $factor;

        return $this;
    }

    /**
     * @param $encounter_rate
     * @param float $block_rate
     * @return $this
     */
    public function set_chance($encounter_rate, $block_rate = 0.5): self
    {
        $this->chance = $encounter_rate;
        $this->block = $block_rate;

        $game = Globals::CurrentGame();
        $factor = $game ? $game->config('zombies.accum') : 1.0;

        if ($factor > 0) $this->chance *= $factor;

        return $this;
    }

    public function get_strength_factor() {
        $s = $this->strength_factor;
        $since = Globals::CurrentGameF()->duration() - $this->last_decay;

        return max(0,min(1,$s + $since * 0.0007));
    }

    public function reduce_strangth_factor($by): void
    {
        $this->strength_factor = $this->get_strength_factor();
        $this->last_decay = Globals::CurrentGameF()->duration();

        $this->strength_factor -= $this->strength_factor * $by;
    }

    /**
     * @param $min
     * @param $max
     * @return Model_Factory_Zombies
     */
    public function set_range($min, $max): \Model_Factory_Zombies
    {
        $this->range = [$min, $max];
        return $this;
    }

    protected function get_game_strength() {
        $curve = Globals::CurrentGameF()->config('zombies.curve');
        if ($curve <= 0) $curve = 0.3;
        return 1 + max(0, (Globals::CurrentGameF()->duration()/2016) - 1) * $curve;
    }

    public function get_strength($include_factor = true) {

        return $this->strength * ($include_factor ? ($this->get_game_strength() * $this->get_strength_factor()) : 1);
    }

    public function get_max_group_count(): int
    {
        return $this->max_adversaries;
    }

    public function reduce_accum(int $num) {
        if ($this->accumulation() <= $num)
            $this->accumulated_zombies = [];
        else
            for ($i = 0; $i < $num; $i++) {

                $target = Tool_Gambling::select(array_keys($this->accumulated_zombies));
                $this->accumulated_zombies[$target]--;
                if ($this->accumulated_zombies[$target] <= 0) unset($this->accumulated_zombies[$target]);

            }
    }

    /**
     * @param null|int $set
     * @return Model_Factory_Zombies|int
     */
    public function accumulation($set = null) {
        if ($set === null)
            return array_reduce($this->accumulated_zombies, function(int $carry, $item) {
                return $carry + $item;
            }, 0);
        else {
            $this->accumulated_zombies = [];

            if ($set > 0) {
                $accum_army = $this->generate_zombie_list($this->get_strength(), $set);

                foreach ($accum_army as $entry) {
                    if (!isset($this->accumulated_zombies[$entry['class']])) $this->accumulated_zombies[$entry['class']] = $entry['count'];
                    else $this->accumulated_zombies[$entry['class']] += $entry['count'];
                }
            }


            return $this;
        }
    }

    public function get_accumulated_zombie_types(): array {
        return $this->accumulated_zombies;
    }

    public function stat_max_zombie_count(): int  {
        $min_str = null;
        foreach ($this->get() as $zcl => $c)
            /** @var Model_Combat_Zombies_Zombie $zcl */
            if ($min_str === null || $min_str > $zcl::get_strength_quantifier())
                $min_str = $zcl::get_strength_quantifier();
        return $min_str > 0 ? floor($this->get_strength(true)/$min_str) : 0;
    }

    public function stat_chance(): float {
        return $this->chance;
    }

    public function stat_blocking_factor(): float {
        return $this->block;
    }

    public function stat_chance_battle(): float {
        return $this->stat_chance() * (1.0 - $this->stat_blocking_factor());
    }

    public function stat_chance_block(): float {
        return $this->stat_chance() * $this->stat_blocking_factor();
    }

    public function release() {

        $ret = [];
        foreach ($this->accumulated_zombies as $z => $count)
            if ($count > 0) {
                /** @var Model_Combat_Zombies_Zombie $z */
                $ret[] = $z::factory()->count($count)->set_distance(random_int($this->range[0], $this->range[1]), 0);
            }

        return $ret;
    }

    public function generate_zombie_list(float $strength, int $fixed_number): array {
        $army = [];
        for ($i = 0; $i < $this->get_max_group_count(); $i++) {
            /** @var Model_Combat_Zombies_Zombie $tmp */
            $tmp = $this->get_element();
            $army[] = $tmp;
        }
        if (empty($army)) return [];

        usort($army, function($a, $b) {
            /**
             * @var Model_Combat_Zombies_Zombie $a
             * @var Model_Combat_Zombies_Zombie $b
             */
            return $b::get_strength_quantifier() - $a::get_strength_quantifier();
        });

        $accum_count = 0;
        $accum_str = $strength;
        $accum_army = [];
        foreach ($army as $zclass) {
            /** @var Model_Combat_Zombies_Zombie $zclass */
            if (!($max_num = floor($accum_str/$zclass::get_strength_quantifier())))
                continue;
            $accum_count += ($num = random_int(1, $max_num));
            $accum_str -= $num * $zclass::get_strength_quantifier();

            $accum_army[] = ['count' => $num, 'class' => $zclass];
        }

        if (!$accum_count)
            return [];

        if ($fixed_number && $accum_count !== $fixed_number) {

            if ($accum_count < $fixed_number) {

                $f = $fixed_number/$accum_count;

                $accum_count = 0;
                foreach ($accum_army as &$entry)
                    $accum_count += ($entry['count'] = ceil($entry['count'] * $f));
                unset($entry);
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

        return $accum_army;
    }

    /**
     * @param bool|false $force
     * @param int        $strength_modifier
     * @param null|int   $fixed_number
     *
     * @return Model_Combat_Zombies_Zombie[]|null
     * @throws Exception
     */
    public function spawn($force = false, $strength_modifier = 1, $fixed_number = null): ?array
    {
        if (!$force && Tool_Gambling::random( Globals::CurrentGameF()->get_zombie_spawn_protection_factor( $this->hideout_mode ) ))
            return null;

        if ($fixed_number === 0 || $fixed_number < 0 || !$this->get_max_group_count() || !($str = $this->get_strength() * $strength_modifier) || (!$force && (mt_rand()/mt_getrandmax()) > $this->stat_chance()))
            return null;

        $accum_army = $this->generate_zombie_list($str, (int)$fixed_number);
        if ($force && empty($accum_army)) $accum_army = [['class' => Model_Combat_Zombies_Shambler::cls(), 'count' => $fixed_number ?? 1]];

        if (!$force && !$fixed_number && Tool_Gambling::random($this->block)) {

            if (Tool_Gambling::random( Globals::CurrentGameF()->get_zombie_block_protection_factor( $this->hideout_mode ) ))
                return null;

            foreach ($accum_army as $entry) {
                if (!isset($this->accumulated_zombies[$entry['class']])) $this->accumulated_zombies[$entry['class']] = $entry['count'];
                else $this->accumulated_zombies[$entry['class']] += $entry['count'];
            }
            return null;
        }

        $ret = [];
        foreach ($accum_army as $entry)
            if ($entry['count'] > 0) {
                /** @var Model_Combat_Zombies_Zombie $z */
                $z = $entry['class'];
                $ret[] = $z::factory()->count($entry['count'])->set_distance(random_int($this->range[0], $this->range[1]), 0);
            }

        return $ret;
    }

    public function dry_spawn($force = false): void
    {
        if (!$this->get_max_group_count() || !$this->get_strength() || (!$force && (mt_rand()/mt_getrandmax()) < $this->stat_chance()))
            return;

        if (!$force && (
                Tool_Gambling::random( Globals::CurrentGameF()->get_zombie_spawn_protection_factor( $this->hideout_mode ) ) ||
                Tool_Gambling::random( Globals::CurrentGameF()->get_zombie_block_protection_factor( $this->hideout_mode ) )
            ))
            return;

        if ($force || (mt_rand()/mt_getrandmax()) < $this->block) {
            $accum_army = $this->generate_zombie_list($this->get_strength(), 0);
            foreach ($accum_army as $entry) {
                if (!isset($this->accumulated_zombies[$entry['class']])) $this->accumulated_zombies[$entry['class']] = $entry['count'];
                else $this->accumulated_zombies[$entry['class']] += $entry['count'];
            }
        }

    }
}	