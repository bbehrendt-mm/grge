<?php /** @noinspection NotOptimalIfConditionsInspection */
defined('SYSPATH') OR die('No direct access allowed.');

class Tool_Events {

    public const TE_MINUTE = 'i';
    public const TE_HOUR = 'G';
    public const TE_DAY = 'j';
    public const TE_MONTH = 'n';
    public const TE_YEAR = 'Y';

    private static $event_plugin_list = [
        'Model_Events_Halloween', 'Model_Events_Xmas', 'Model_Events_Easter',
        'Model_Events_Fools', 'Model_Events_Midness'
    ];

    /**
     * @return Model_Events_Event[]|string[]
     */
    private static function get_plugins(): array {
        return static::$event_plugin_list;
    }

    private static function get($what, $time = null): int
    {
        return $time ? (int)date($what, $time) : (int)date($what);
    }

    public static function ticket_event($time = null): bool
    {
        foreach (static::current_events() as $event)
            if ($event::additional_effect(Model_Events_Event::MEE_EFFECT_TICKET))
                return true;
        return false;
    }

    public static function handle_event_triggers(?int $time = null): void
    {
        if (!Globals::hasCurrentGame()) return;

        foreach (static::current_events() as $current_event)
            if (($key = $current_event::get_key()) && !Globals::CurrentGameF()->get_initialized_event($key))
                new $current_event();
    }

    /**
     * @param int|null $time
     * @return string[]|Model_Events_Event[]
     * @throws Exception
     */
    public static function current_events(?int $time = null): array {
        return array_map(function($e) { return $e[2]; }, static::current_events_info());
    }


    public static function current_events_info(?int $time = null): array {
        $now = new DateTime();
        $now->setTimestamp( $time ?: time() );
        $ret = [];

        foreach (static::get_plugins() as $plugin)
            if ($plugin::check_season($now,$a,$b))
                $ret[] = [$a,$b,$plugin];

        usort( $ret, function($a,$b) { if ($a[0] == $b[0]) return 0; return ($a[0] < $b[0]) ? -1 : 1; } );
        return $ret;
    }


    public static function next_events_info(?DateTime $until = null): array {
        $now = new DateTime();
        $until = $until ?: (new DateTime())->add(new DateInterval('P60D'));
        $ret = [];

        foreach (static::get_plugins() as $plugin) {
            $i = 0;
            while ( $plugin::get_season($a,$b, $i) && $a < $until && $i < 5 ) {
                if ($a > $now)
                    $ret[] = [$a,$b,$plugin];
                $i++;
            }
        }

        usort( $ret, function($a,$b) { if ($a[0] == $b[0]) return 0; return ($a[0] < $b[0]) ? -1 : 1; } );
        return $ret;
    }

    public static function current_skin(?int $time = null): ?string
    {
        $final = null;
        foreach (static::current_events($time) as $event) {
            $key = $event::get_key();
            if ($key && file_exists(APPPATH . "assets/skins/$key"))
                $final = $key;
        }
        return $final;
    }

    public static function is_april_fools(?int $time = null): bool
    {
        foreach (static::current_events() as $event)
            if ($event::additional_effect(Model_Events_Event::MEE_EFFECT_FOOLS))
                return true;
        return false;
    }

    public static function is_october_midness(?int $time = null): bool
    {
        foreach (static::current_events() as $event)
            if ($event::additional_effect(Model_Events_Event::MEE_EFFECT_UNLOCK))
                return true;
        return false;
    }

    public static function maintenance(?int $time = null): bool
    {
        return (bool)static::active_maintenance_period($time);
    }

    public static function active_maintenance_period(?int $time = null) {
        $time = $time ?: time();
        $current = ($time - strtotime(date('Y-m-d'), $time))/60;

        foreach (Kohana::$config->load('server.downtime') as $period)
            if ($period[0] <= $current && $period[1] > $current)
                return $period;
        return null;
    }
}