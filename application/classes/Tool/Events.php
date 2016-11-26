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

    /**
     * @param string|null $event
     * @param int|null $time
     * @return null|string|Model_Events_Event
     */
    public static function event_extended_classes($event = null, $time = null) {
        if ($event === null) $event = static::current($time);

        switch ($event) {
            case 'halloween': return 'Model_Events_Halloween'; break;
            case 'xmas': return 'Model_Events_Xmas'; break;
            default: return null;
        }

    }

    public static function event_extended_name($event = null, $time = null) {
        if ($cls = static::event_extended_classes($event, $time))
            return $cls::name();
        else return null;
    }

    public static function handle_event_triggers($time = null) {
        /** @global Model_Game $game */
        global $game;

        if (!$game) return;
        $ev = static::current($time);
        if (($cls = static::event_extended_classes($ev)) && !$game->get_initialized_event($ev))
            new $cls;
    }

    public static function current($time = null) {
        return 'xmas';

        //Detect halloween (30.10. - 05.11.)
        if ( (static::get(static::TE_MONTH, $time) == 10 && static::get(static::TE_DAY, $time) >= 30) || (static::get(static::TE_MONTH, $time) == 11 && static::get(static::TE_DAY, $time) <= 5) )
            return 'halloween';

        //Detect christmas (6.12. - 26.12.)
        if (static::get(static::TE_MONTH, $time) == 12 && static::get(static::TE_DAY, $time) >= 6 && static::get(static::TE_DAY, $time) <= 26 )
            return 'xmas';

        //Detect new year (30.12. - 02.01.)
        if ( (static::get(static::TE_MONTH, $time) == 12 && static::get(static::TE_DAY, $time) >= 30) || (static::get(static::TE_MONTH, $time) == 1 && static::get(static::TE_DAY, $time) <= 2) )
            return 'newyear';

        //Detect easter (25.03.2016 - 01.04.2016)
        switch (static::get(static::TE_YEAR, $time)) {
            case 2016:
                if (static::get(static::TE_MONTH, $time) == 3 && static::get(static::TE_DAY, $time) >= 25) return 'easter';
                break;
        }

        return null;
    }

    public static function current_skin($time = null) {
        $ev = static::current($time);
        if ($ev && file_exists(APPPATH . "assets/skins/$ev"))
            return $ev;
        else return null;
    }

    public static function is_april_fools() {
        /**
         * @global Model_Game $game
         * @global Model_Player $player
         */
        global $game, $player;
        return (static::get(static::TE_MONTH) == 4 && static::get(static::TE_DAY) == 1) && ($game->duration() > 300) && !$player->april_fools();
    }

    public static function is_october_midness() {
        return (static::get(static::TE_MONTH) == 10 && static::get(static::TE_DAY) == 14);
    }

    public static function maintenance($time = null) {
        return (bool)static::active_maintenance_period($time);
    }

    public static function active_maintenance_period($time = null) {
        $time = $time ? $time : time();
        $current = ($time - strtotime(date('Y-m-d'), $time))/60;

        foreach (Kohana::$config->load('server.downtime') as $period)
            if ($period[0] <= $current && $period[1] > $current)
                return $period;
        return null;
    }
}