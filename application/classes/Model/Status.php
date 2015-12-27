<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Status {

    const MS_STAT_HUNGER = 1;
    const MS_STAT_THIRST = 2;
    const MS_STAT_HEALTH = 3;
    const MS_STAT_SLEEPY = 4;
    const MS_STAT_ENERGY = 5;
    const MS_STAT_DRUNK  = 6;
    const MS_STAT_RADIATION = 7;
    const MS_STAT_ZOMBIFY = 8;
    const MS_STAT_FREEZE = 9;

    const MS_STATUS_COUNT = 9;
    const MS_THRESHOLD = 512;

    const MS_CHAR_DISTANCING = 512;
    const MS_CHAR_EVASIVENESS = 513;
    const MS_CHAR_ACCURACY = 514;
    const MS_CHAR_DAMAGE_RESISTANCE = 515;
    const MS_CHAR_BULKYNESS = 516;
    const MS_CHAR_DAMAGE_MULTIPLIER = 517;
    const MS_CHAR_LOCATION_SPAWNRATE = 518;

    const MS_EFFECT_GENERIC = 0;
    const MS_EFFECT_ITEM = 1;
    const MS_EFFECT_BUFF = 2;
    const MS_EFFECT_MOVEMENT = 3;
    const MS_EFFECT_GLOBAL = 4;
    const MS_EFFECT_UNSCALE = 5;
    const MS_EFFECT_REQUIREMENT = 5;

    /** @var Model_Buffs_Abstract_Buff[] */
    protected $buffs = [];

    protected $status_bars = [];
    protected $scaling_effects = [];

    protected $cod = null;
    protected $alive = true;

    public function __wakeup() {
        //Reset above-threshold bars
        foreach ($this->status_bars as $key => &$value)
            if ($key >= Model_Status::MS_THRESHOLD) $value = 1;

        if ($this->alive())
            $this->clear_cause_of_death();
    }

    /**
     * Refreshes all char values
     */
    public function refresh_char() {
        $tmp = Array();
        foreach (array_keys($this->status_bars) as $stat) if ($stat >= Model_Status::MS_THRESHOLD) {
            $tmp[] = $stat;
            $tmp[] = $this->buffs_by_stat($stat);
        }

        $this->modify($tmp, static::MS_EFFECT_UNSCALE);
    }

    /**
     * Returns a specific status value; if this value has not been set, returns 0
     * @param int $stat
     * @return int
     */
    public function get($stat) {
        if (!isset($this->status_bars[$stat])) return ($stat >= Model_Status::MS_THRESHOLD) ? 1 : 0;
        elseif ($stat >= Model_Status::MS_THRESHOLD)
            return max(0,1 + $this->buffs_by_stat($stat));
        else return $this->status_bars[$stat];
    }

    /**
     * Sets players stats (ignoring their previous values) and rebuilds buffers afterwards
     * @param number|array $args,... Supposed to be in this format: [stat1, newval1, stat2, newval2, ...]
     * @throws Exception When $args is wrong format
     */
    public function set($args) {
        if (!is_array($args)) $args = func_get_args();

        //Make sure the input format is correct
        if (count($args) % 2) throw new Exception('Invalid input format for stat modificator!');

        //Run over each input pair
        $i = 0;
        while ($i < count($args)) {
            //Set new value
            $this->status_bars[$args[$i]] = $args[$i+1];

            //Enforce bounds (0/100)
            $this->status_bars[$args[$i]] = min(max($this->status_bars[$args[$i]],0),100);

            //Jump to next pair
            $i += 2;
        }

        $this->rebuild_buffs();
    }

    /**
     * Returns the scaling factor for a given stat
     * @param int $stat Status
     * @param int $type Effect Type
     * @return number
     */
    public function scaling($stat, $type) {
        if (!isset($this->scaling_effects[$stat]) || $stat >= Model_Status::MS_THRESHOLD || $type == static::MS_EFFECT_UNSCALE)
            return 1;

        return
            (
                (isset($this->scaling_effects[$stat][$type]) && !empty($this->scaling_effects[$stat][$type]))
                    ? array_reduce($this->scaling_effects[$stat][$type], function($a, $b) {return $a * $b;}, 1)
                    : 1
            ) * (
                ($type !== static::MS_EFFECT_GLOBAL) ? $this->scaling($stat, static::MS_EFFECT_GLOBAL) : 1
            );
    }

    public function scaling_add($stat, $type, $name, $value) {
        if ($type == static::MS_EFFECT_UNSCALE) return;
        if (!isset($this->scaling_effects[$stat])) $this->scaling_effects[$stat] = [$type => []];
        $this->scaling_effects[$stat][$type][$name] = $value;
    }

    public function scaling_remove($stat, $type, $name) {
        if (isset($this->scaling_effects[$stat]) && isset($this->scaling_effects[$stat][$type]) && isset($this->scaling_effects[$stat][$type][$name]))
            unset($this->scaling_effects[$stat][$type][$name]);
    }

    public function miss($stat, $req, $type) {
        if ($stat >= Model_Status::MS_THRESHOLD || $req <= 0) return 0;
        elseif (!isset($this->status_bars[$stat])) return $req;
        else return max(0,$req * $this->scaling($stat, $type) - $this->status_bars[$stat]);
    }

    /**
     * Returns true if the player fullfills all given status requirements
     * @param number|array $args,...  Supposed to be in this format: [stat1, req1, stat2, req2, ...]. Character effects (CHAR) will be ignored!
     * @param int $type
     * @return bool
     */
    public function has($args, $type = Model_Status::MS_EFFECT_UNSCALE) {
        if (!is_array($args)) {
            $args = func_get_args();
            $type = (count($args) % 2) ? array_splice($args, -1, 1)[0] : static::MS_EFFECT_UNSCALE;
        }

        //Run over each input pair
        $i = 0;
        while ($i < count($args)) {
            if ($args[$i] >= Model_Status::MS_THRESHOLD) continue;
            elseif (!isset($this->status_bars[$args[$i]]) || ($this->status_bars[$args[$i]] < $args[$i+1] * $this->scaling($args[$i], $type))) return false;

            //Jump to next pair
            $i += 2;
        }

        return true;
    }

    /**
     * Changes players stats and rebuilds buffers afterwards
     * @param number|array $args,... Supposed to be in this format: [stat1, change1, stat2, change2, ...]
     * @param int $type
     * @throws Exception When $args is wrong format
     */
    public function modify($args, $type = Model_Status::MS_EFFECT_UNSCALE) {
        if (!is_array($args)) {
            $args = func_get_args();
            $type = (count($args) % 2) ? array_splice($args, -1, 1)[0] : static::MS_EFFECT_UNSCALE;
        }

        //Run over each input pair
        $i = 0;
        while ($i < count($args)) {
            //Check if value is set already and calculate change
            if (!isset($this->status_bars[$args[$i]]))
                $this->status_bars[$args[$i]] = ($args[$i] >= Model_Status::MS_THRESHOLD) ? 1 : 0;

            $this->status_bars[$args[$i]] += $args[$i+1] * $this->scaling($args[$i], $type);

            //Enforce bounds (0/100)
            $this->status_bars[$args[$i]] = min(max($this->status_bars[$args[$i]],0),100);

            //Jump to next pair
            $i += 2;
        }

        $this->rebuild_buffs();
    }

    /**
     * Rebuilds all buffs
     */
    private function rebuild_buffs() {
        /**
         * @var $buff Model_Buffs_Abstract_Buff
         */
        foreach ($this->buffs as $buff)
            $buff->rebuild();
    }

    public function rebuild() {
        $this->rebuild_buffs();
    }

    public function tick() {
        $tmp = [];
        foreach (array_keys($this->status_bars) as $stat) {
            $tmp[] = $stat;
            $tmp[] = $this->buffs_by_stat($stat);
        }


        $this->modify($tmp, static::MS_EFFECT_BUFF);

        foreach ($this->buffs as $buff) $buff->tick();
    }

    /**
     * Adds a new buff
     * @param Model_Buffs_Abstract_Buff $buff
     */
    public function add(Model_Buffs_Abstract_Buff $buff) {
        /**
         * @var $p Model_Buffs_Abstract_Buff|null
         */

        //Get existing buff
        $p = isset($this->buffs[$buff->bid()]) ? $this->buffs[$buff->bid()] : null;

        if ($p) {
            if ($p->get_dominance() > $buff->get_dominance()) return;
            elseif ($p->get_dominance() < $buff->get_dominance()) $this->buffs[$buff->bid()] = $buff;
            else $p->merge($buff);
        } else $this->buffs[$buff->bid()] = $buff;
    }

    /**
     * Removes a buff
     * @param Model_Buffs_Abstract_Buff|string $obj
     */
    public function remove($obj) {
        if (is_object($obj)) {
            $obj->remove();
            unset($this->buffs[$obj->bid()]);
        } else {
            $tmp = explode('/', $obj);
            $obj = $tmp[0];

            if (isset($this->buffs[$obj]) && isset($tmp[1]) && $this->retrieve($obj)->abid() != $tmp[1])
                return;

            if (isset($this->buffs[$obj]))
                /** @noinspection PhpUndefinedMethodInspection */
                $this->buffs[$obj]->remove();
            unset($this->buffs[$obj]);
        }
    }

    /**
     * If a buff specified by $id is set, this function retrieves it, otherwise NULL is returned.
     * @param string $id
     * @return Model_Buffs_Abstract_Buff
     */
    public function retrieve($id) {
        $tmp = explode('/', $id);
        $id = $tmp[0];

        /** @noinspection PhpUndefinedMethodInspection */
        if (isset($this->buffs[$id]) && isset($tmp[1]) && $this->buffs[$id]->abid() != $tmp[1])
            return NULL;

        if (isset($this->buffs[$id])) return $this->buffs[$id];
        else return NULL;
    }

    /**
     * Return status effects one one specific stat caused by buffs
     * @param int $stat
     * @return int
     */
    final public function buffs_by_stat($stat) {
        /**
         * @var $buff Model_Buffs_Abstract_Buff
         */
        $raise_acc = $drop_acc = 0;
        $raise_prc = $drop_prc = 1;

        foreach ($this->buffs as $buff) {
            $raise_acc += $buff->effect($stat, Model_Buffs_Abstract_Buff::MB_RAISE_ACC);
            $raise_prc += $buff->effect($stat, Model_Buffs_Abstract_Buff::MB_RAISE_PRC);
            $drop_acc += $buff->effect($stat, Model_Buffs_Abstract_Buff::MB_DROP_ACC);
            $drop_prc += $buff->effect($stat, Model_Buffs_Abstract_Buff::MB_DROP_PRC);
        }

        return max(0,($raise_acc * max($raise_prc,0))) - max(0,($drop_acc * max($drop_prc,0)));
    }


    /** @return Model_Buffs_Abstract_Buff[] */
    public function buffs() {
        return $this->buffs;
    }

    public function get_cause_of_death() {
        return $this->cod;
    }

    public function set_cause_of_death($d) {
        if ($this->alive())
            $this->cod = $d;
    }

    public function clear_cause_of_death() {
        $this->set_cause_of_death(null);
    }

    public function alive($set = null) {
        if ($set !== null) $this->alive = $set;
        return $this->alive;
    }
}