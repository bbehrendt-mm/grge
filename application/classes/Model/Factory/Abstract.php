<?php defined('SYSPATH') OR die('No direct access allowed.');

abstract class Model_Factory_Abstract extends Model {

    protected static $base;
    protected static $expected_result_class;

    protected $group = 'default';
    protected $import_from = null;

    protected $cache = [];
    protected $equalized = [];

    public static function load($location, $group = 'default', $fallback = ['default']) {
        if ($location === null) {
            $s = static::class;
            return new $s();
        }
        else {
            $fallback = is_array($group) ? $group : array_merge([$group], $fallback);
            $list = Tool_System::instance_of($location, 'Model_Places_Abstract_Place') ? Tool_System::get_class_hierarchy($location) : [$location];
            foreach ($list as $l_entry)
                foreach ($fallback as $f_entry)
                    if ($tmp = Tool_System::simple_config(static::$base . "/{$f_entry}/{$l_entry}"))
                        return $tmp;
            return null;
        }
    }

    /**
     * Returns the percentage table
     * @return array
     */
    public function get() {
        return $this->equalized;
    }

    /**
     * @param null|string|Model_Factory_Abstract $elem
     * @param $count
     * @return $this
     */
    public function add($elem, $count) {
        if ($count > 0 && $elem)
            $this->cache[] = ['chance' => $count, 'value' => $elem];

        return $this;
    }

    /**
     * @param string $group
     * @param null $import_from
     * @return $this
     */
    public static function factory($group = 'default', $import_from = null) {
        $s = static::class;
        return new $s($group, $import_from);
    }

    /**
     * @param $location
     * @param string $group
     * @param array $fallback
     * @return null|Model_Factory_Abstract
     */
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
     * @return $this
     */
    protected function clean() {
        $this->cache = [];
        return $this;
    }

    /**
     * @param null|[] $resolve_import_paths
     * @return $this
     */
    public function equalize($resolve_import_paths = null) {
        $gs = 0;
        $s = static::class;

        if (!$resolve_import_paths)
            $resolve_import_paths = array_merge([$this->group], $this->import_from ? $this->import_from : []);

        foreach ($this->cache as $entry)
            $gs += $entry['chance'];

        $this->equalized = [];
        foreach ($this->cache as $entry) {
            $c = $entry['chance'] / $gs;

            if (!Tool_System::instance_of($entry['value'], [$s, static::$expected_result_class]))
                $entry['value'] = static::load($entry['value'], $resolve_import_paths);

            if (!$entry['chance'] || !$entry['value'])
                continue;

            if (Tool_System::instance_of($entry['value'], $s)) {
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
     * Forces the recalculation of the percentage table, so they add up to 100%.
     */
    protected function realign() {
        $accum = 0;
        foreach ($this->equalized as $c)
            $accum += $c;

        if ($accum > 0)
            foreach ($this->equalized as $k => $c)
                $this->equalized[$k] /= $accum;
    }

    /**
     * @return null|string
     */
    public function get_element() {
        $accum = 0;
        $r = (mt_rand()/mt_getrandmax());
        foreach ($this->equalized as $k => $c)
            if ($r < ($accum += $c))
                return $k;
        return null;
    }
}	