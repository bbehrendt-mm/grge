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

    /**
     * @param string|Model_Items_Abstract_Item $cls 
     * @param int $inst
     * @return null
     */
    public static function getItemInstanceName($cls, $inst = -1) {
        if (!Tool_System::instance_of($cls, Model_Items_Abstract_Item::cls()))
            return null;

        $reflection = new ReflectionClass($cls);
        $parameters = $reflection->getConstructor()->getParameters();
        
        /** @var Model_Items_Abstract_Item $instance */
        $instance =
            ($reflection->isInstantiable() && $cls::getNumberOfTypes() > 0) && ($reflection->getConstructor()->getNumberOfRequiredParameters() == 0) && ($parameters[0]->getName() == 'type')
                ? new $cls($inst < 0 || $inst > ($cls::getNumberOfTypes() - 1) ? 0 : $inst) : null;
        $singular = $cls::getNumberOfTypes() == 1;

        if ($cls < 0 && $cls::static_name()) return $cls::static_name();
        
        if ($instance && ($inst >= 0 || $singular || !$cls::static_name())) return $instance->name();
        elseif ($cls::static_name()) return $cls::static_name();
        else return null;
    }

    /**
     * @param string|Model_Items_Abstract_Item $cls
     * @param int $inst
     * @return null
     */
    public static function getItemInstanceIcon($cls, $inst = -1) {
        if (!Tool_System::instance_of($cls, Model_Items_Abstract_Item::cls()))
            return null;

        $reflection = new ReflectionClass($cls);
        $parameters = $reflection->getConstructor()->getParameters();

        /** @var Model_Items_Abstract_Item $instance */
        $instance =
            ($reflection->isInstantiable() && $cls::getNumberOfTypes() > 0) && ($reflection->getConstructor()->getNumberOfRequiredParameters() == 0) && (isset($parameters[0]) && $parameters[0]->getName() == 'type')
                ? new $cls($inst < 0 || $inst > ($cls::getNumberOfTypes() - 1) ? 0 : $inst) : null;

        if (!$instance && $inst >= 0) return null;

        $singular = $cls::getNumberOfTypes() == 1;

        if ($cls < 0 && $cls::static_icon()) return $cls::static_icon();

        if ($instance && ($inst >= 0 || $singular || !$cls::static_icon())) return $instance->icon();
        elseif ($cls::static_icon()) return $cls::static_icon();
        else return null;
    }

    public static function getSeasonTitle($num_season = null) {
        $titles = Kohana::$config->load('server.titles');
        $season = Kohana::$config->load('server.season');

        if ($num_season !== null && $num_season > $season) return null;
        elseif ($num_season !== null)
            return isset($titles[$num_season]) ? $titles[$num_season] : 'Mysteriöse Season';
        else {
            for ($i = 0; $i <= $season; $i++)
                if (!isset($titles[$i])) $titles[$i] = 'Mysteriöse Season';
            return $titles;
        }
    }
}
