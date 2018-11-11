<?php defined('SYSPATH') OR die('No direct access allowed.');

class Tool_Htmlout {

    /**
     * @param $id
     *
     * @return bool|string
     * @throws Kohana_Exception
     */
    public static function get_external_link($id) {
        /** @var array $links */
        $links = Kohana::$config->load('services.links.' . $id);
        if (!$links) return null;
        elseif (is_string($links)) return $links;
        else {
            $lang = explode('-', I18n::$lang)[0];
            if (isset($links[$lang])) return $links[$lang];
            if (isset($links['default'])) return $links['default'];
            return array_values($links)[0];
        }
    }
}	