<?php defined('SYSPATH') OR die('No direct access allowed.');

class Tool_Events {

    const TE_MINUTE = 'i';
    const TE_HOUR = 'G';
    const TE_DAY = 'j';
    const TE_MONTH = 'n';
    const TE_YEAR = 'Y';

    private static function get($what, $time = null) {
        return $time ? (int)date($what, $time) : (int)date($what);
    }

    public static function ticket_event($time = null) {
        return in_array(static::current($time),['xmas','easter']);
    }

    public static function current($time = null) {
        return null;

        //Detect halloween (30.10. - 05.11.)
        if ( (static::get(static::TE_MONTH, $time) == 10 && static::get(static::TE_DAY, $time) >= 30) || (static::get(static::TE_MONTH, $time) == 11 && static::get(static::TE_DAY, $time) <= 5) )
            return 'halloween';

        //Detect christmas (6.12. - 26.12.)
        if ( static::get(static::TE_MONTH, $time) == 12 && static::get(static::TE_DAY, $time) >= 6 && static::get(static::TE_DAY, $time) <= 26 )
            return 'xmas';

        //Detect new year (30.12. - 02.01.)
        if ( (static::get(static::TE_MONTH, $time) == 12 && static::get(static::TE_DAY, $time) >= 30) || (static::get(static::TE_MONTH, $time) == 1 && static::get(static::TE_DAY, $time) <= 2) )
            return 'newyear';

        //Detect easter (18.04.2014 - 24.04.2014)
        switch (static::get(static::TE_YEAR, $time)) {
            case 2014:
                if (static::get(static::TE_MONTH, $time) == 4 && static::get(static::TE_DAY, $time) >= 18 && static::get(static::TE_DAY, $time) <= 24 ) return 'easter';
                break;
        }

        return null;
    }

    public static function is_april_fools() {
        global $game, $player;
        return (static::get(static::TE_MONTH) == 4 && static::get(static::TE_DAY) == 1) && ($game->duration() > 300) && !$player->april_fools();
    }

    public static function is_october_midness() {
        return (static::get(static::TE_MONTH) == 10 && static::get(static::TE_DAY) == 14);
    }

    public static function maintenance($time = null) {
        $data = Kohana::$config->load('server.downtime');
        return ($data['h'] == static::get(static::TE_HOUR, $time) && $data['start'] <= static::get(static::TE_MINUTE, $time) && $data['finish'] > static::get(static::TE_MINUTE, $time));
    }
}