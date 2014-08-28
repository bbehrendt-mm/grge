<?php defined('SYSPATH') OR die('No direct access allowed.');

class Tool_Htmlout {

    public static function build(&$std_response, &$view) {
        $std_response->body(
                Request::factory('widget/menu' )->execute()->body() .
                Request::factory('widget/clock')->execute()->body() .
                Request::factory('widget/syslogd')->execute()->body() .
                $view
        );
    }

    public static function is_mobile() {
        if (!isset($_SERVER['HTTP_USER_AGENT'])) return false;
        return (preg_match("/phone|mobile|iphone|itouch|ipod|symbian|android|htc_|htc-|palmos|blackberry|opera mini|iemobile|windows ce|nokia|fennec|hiptop|kindle|mot |mot-|webos\/|samsung|sonyericsson|^sie-|nintendo/", strtolower($_SERVER['HTTP_USER_AGENT'])));
    }

    public static function device() {
        if (!static::is_mobile()) return 'desktop';

        if (preg_match('/(tablet|ipad|playbook)|(android(?!.*(mobi|opera mini)))/i', strtolower($_SERVER['HTTP_USER_AGENT']))) {
            return 'tablet';
        }

        return 'mobile';
    }

    /**
     * @param Kohana_Request|null $request
     * @return string|null
     */
    public static function get_key($request) {
        if (!$request) return null;

        if ($key = $request->current()->post('key')) return $key;
        if ($key = $request->current()->query('key')) return $key;
        if ($key = $request->current()->query('k')) return $key;
    }

    /**
     * @param Kohana_Request|null $request
     * @return string|null
     */
    public static function get_host($request) {
        if (!$request) return null;

        if ($host = $request->current()->query('h')) return $host;
        foreach (Kohana::$config->load('mt.links') as $v) if ($v['token'])
            if (strpos($request->referrer(), $v['url']) !== false)
                return $v['name'];

        return null;
    }

    /**
     * @param $id
     * @return bool|string
     */
    public static function get_external_link($id) {
        $lang = explode('-', I18n::$lang);
        $lang = $lang[0];

        if ($s = Kohana::$config->load('server.links.' . $id . '.' . $lang))
            return $s;
        else return false;
    }
}	