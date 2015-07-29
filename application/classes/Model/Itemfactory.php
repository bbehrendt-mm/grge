<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Itemfactory extends Model {

    private $cache = [];
    private $equalized = [];
    private $fillrate = 1;
    private $decay = 0.1;

    private $group = 'default';
    private $import_from = null;

    public static function load($location, $group = 'default', $fallback = ['default']) {
        if ($location === null)
            return new Model_Itemfactory();
        else {
            $fallback = is_array($group) ? $group : array_merge([$group], $fallback);
            $list = Tool_System::instance_of($location, 'Model_Places_Abstract_Place') ? Tool_System::get_class_hierarchy($location) : [$location];
            foreach ($list as $l_entry)
                foreach ($fallback as $f_entry)
                    if ($tmp = Tool_System::simple_config("items_2/{$f_entry}/{$l_entry}"))
                        return $tmp;
            return null;
        }
    }

    public static function factory($group = 'default', $import_from = null) {
        return new Model_Itemfactory($group, $import_from);
    }

    public static function read($location, $group = 'default', $fallback = ['default']) {
        $tmp = static::load($location, $group, $fallback);
        return $tmp ? $tmp->equalize()->clean() : null;
    }

    public function __construct($group = 'default', $import_from = null) {
        $this->group = $group;
        if ($import_from && is_string($import_from))
            $import_from = [$import_from];
        $this->import_from = $import_from;
    }

    /**
     * @param null|string|Model_Itemfactory $elem
     * @param $count
     * @return Model_Itemfactory
     */
    public function add($elem, $count) {
        if ($count > 0 && $elem)
            $this->cache[] = ['chance' => $count, 'value' => $elem];

        return $this;
    }

    /**
     * @param number $d
     * @return Model_Itemfactory
     */
    public function set_decay_factor($d) {
        $this->decay = $d;
        return $this;
    }

    /**
     * @return Model_Itemfactory
     */
    private function clean() {
        $this->cache = null;
        return $this;
    }

    /**
     * @param number $d
     * @return Model_Itemfactory
     */
    public function set_fillrate($d) {
        $this->fillrate = $d;
        return $this;
    }

    /**
     * @param null|[] $resolve_import_paths
     * @return Model_Itemfactory
     */
    public function equalize($resolve_import_paths = null) {
        $gs = 0;

        if (!$resolve_import_paths)
            $resolve_import_paths = array_merge([$this->group], $this->import_from ? $this->import_from : []);

        foreach ($this->cache as $entry)
            $gs += $entry['chance'];

        $this->equalized = [];
        foreach ($this->cache as $entry) {
            $c = $entry['chance'] / $gs;

            if (!Tool_System::instance_of($entry['value'], ['Model_Itemfactory', 'Model_Items_Abstract_Item']))
                $entry['value'] = static::load($entry['value'], $resolve_import_paths);

            if (!$entry['chance'] || !$entry['value'])
                continue;

            if (Tool_System::instance_of($entry['value'], 'Model_Itemfactory')) {
                /** @noinspection PhpUndefinedMethodInspection */
                $entry['value']->equalize($resolve_import_paths);
                $d = $entry['value']->equalized;
            } else $d = [$entry['value'] => 1];

            foreach ($d as $dk => $dp) {
                if (!isset($this->equalized[$dk])) $this->equalized[$dk] = 0;
                $this->equalized[$dk] += $c * $dp;
            }
        }

        return $this;
    }

    /**
     * @param bool $force
     * @param bool $apply_decay
     * @return null|Model_Items_Abstract_Item
     */
    public function spawn($force = false, $apply_decay = true) {
        if (!$this->equalized || (!$force && (mt_rand()/getrandmax()) > $this->fillrate))
            return null;

        $accum = 0;
        foreach ($this->equalized as $k => $c)
            if ((mt_rand()/getrandmax()) < ($accum += $c)) {
                if ($apply_decay) {
                    $this->fillrate -= $this->fillrate * $this->decay;
                    $this->equalized[$k] -= $this->equalized[$k] * $this->decay;

                    $this->realign();
                }
                return new $k;
            }
        return null;
    }

    private function realign() {
        $accum = 0;
        foreach ($this->equalized as $c)
            $accum += $c;

        if ($accum > 0)
            foreach ($this->equalized as $k => $c)
                $this->equalized[$k] /= $accum;
    }

    public function replenish($factor = 1) {
        $this->fillrate += (1 - $this->fillrate) * $factor;
        return $this;
    }

    public function get() {
        return $this->equalized;
    }

    public function findings_left() {
        $f = $this->fillrate;
        $r = 0;
        if (!$this->equalized) return 0;
        elseif ($this->decay <= 0) return PHP_INT_MAX;
        while ($f > 0.05) {
            $f -= $f * $this->decay;
            $r++;
        }
        return $r;
    }
}	