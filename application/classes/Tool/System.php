<?php defined('SYSPATH') OR die('No direct access allowed.');

class Tool_System {

    public static $cache = array();

    /**
     * @param string|object $class Class to test
     * @param string|object|array $super Super class
     * @return bool True, when $class is an instance of or derived from $super
     */
    public static function instance_of($class, $super) {
		if (is_array($super)) {
            foreach ($super as $elem)
                if (static::instance_of($class,$elem)) return true;
            return false;
        }

        $class = (is_object($class)) ? get_class($class) : $class;
		$super = (is_object($super)) ? get_class($super) : $super;

        if (!class_exists($class) || (!class_exists($super) && !interface_exists($super)))
            return false;
		
		return ($class === $super || is_subclass_of($class, $super) || in_array($super, class_implements($class)));
	}

    /**
     * Returns an array containing all parent classes of this object in order
     * @param Object|string $obj Object or class name
     * @return array
     */
    public static function get_class_hierarchy($obj) {
        $hierarchy = [];
        if (is_object($obj) || is_string($obj)) {
            $class = is_object($obj) ? get_class($obj) : $obj;
            do {
                $hierarchy[] = $class;
            } while (($class = get_parent_class($class)) !== false);
        }
        return $hierarchy;
    }

    public static function simple_config($path) {
        if (!file_exists(APPPATH . 'config/' . $path . EXT))
            return null;
        else /** @noinspection PhpIncludeInspection */
            return include(APPPATH . '/config/' . $path . EXT);
    }

    /**
     * Accumulated configuration entries using classnames as key according to a given derived class instance
     * @param string|array $base Config object to use as base; when given as string, it is interpreted as path to a Kohana config object
     * @param string|object $subject Class instance
     * @return array Accumulated data
     */
    public static function config_tree($base, $subject) {
        if (!$subject)
            return null;
        if (is_object($subject))
            $subject = get_class($subject);

        $accum = array();

        if (!is_array($base) && isset(Tool_System::$cache[$base . "." . $subject]))
            return Tool_System::$cache[$base . "." . $subject];

        $tree = Array();
        while ($subject !== false) {
            $tree[] = $subject;
            $subject = get_parent_class($subject);
        }
        $tree = array_reverse($tree);

        foreach ($tree as $subject)
            if ($level = is_array($base) ? (isset($base[$subject]) ? $base[$subject] : array()) : Kohana::$config->load($base . '.' . $subject))
                /** @noinspection PhpParamsInspection */
                $accum = array_merge($accum, $level);

        if (!is_array($base))
            Tool_System::$cache[$base . "." . $subject] = $accum;

        return $accum;
    }

    public static function getClassID($class) {
        if (is_object($class)) $class = get_class($class);
        /*$cache = Cache::instance();
        $addr = $cache->get('classes.' . $class, substr(md5($class . '__salt'), 0, 5));
        $cache->set('classes.' . $class, $addr);
        return $addr;*/

        return substr(md5($class . '__salt'), 0, 5);
    }
}
