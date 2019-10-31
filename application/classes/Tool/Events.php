<?php /** @noinspection NotOptimalIfConditionsInspection */
defined('SYSPATH') OR die('No direct access allowed.');

class Tool_Events {

    public const TE_MINUTE = 'i';
    public const TE_HOUR = 'G';
    public const TE_DAY = 'j';
    public const TE_MONTH = 'n';
    public const TE_YEAR = 'Y';

    private static $event_plugin_list = ['Model_Events_Halloween', 'Model_Events_Xmas', 'Model_Events_Easter'];

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
        $cls = static::event_extended_classes(null,$time);
        return $cls === null ? false : $cls::uses_ticket();
    }

    /**
     * @param string|null $event
     * @param int|null $time
     * @return null|string|Model_Events_Event
     */
    public static function event_extended_classes($event = null, ?int $time = null) {
        if ($event === null) $event = static::current($time);

        foreach (static::get_plugins() as $plugin)
            if ($plugin::get_key() === $event) return $plugin;
        return null;
    }

    public static function event_extended_name($event = null, ?int $time = null): ?string
    {
        if ($cls = static::event_extended_classes($event, $time))
            return $cls::name();
        else return null;
    }

    public static function handle_event_triggers(?int $time = null): void
    {
        if (!Globals::hasCurrentGame()) return;
        $ev = static::current($time);
        if (($cls = static::event_extended_classes($ev)) && !Globals::CurrentGameF()->get_initialized_event($ev))
            new $cls();
    }

    public static function current(?int $time = null): ?string
    {
        if ($ev = Kohana::$config->load('basic.event')) return $ev;

        $now = new DateTime();
        $now->setTimestamp($time ?: time());
        foreach (static::get_plugins() as $plugin)
            if ($plugin::check_season($now)) return $plugin::get_key();

        return null;
    }

    public static function next_events(?DateTime $until = null): array {
        $now = new DateTime();
        $until = $until ?: (new DateTime())->add(new DateInterval('P600D'));
        $ret = [];

        foreach (static::get_plugins() as $plugin) {
            $i = 0;
            while ( $plugin::get_season($a,$b, $i) && $a < $until && $i < 5 ) {
                if ($a > $now) {
                    Controller::dump('event', [ $plugin::get_key(), $i, $a->format('r'), $b->format('r') ]);
                    $ret[] = [$a,$b,$plugin];
                }
                $i++;
            }
        }

        usort( $ret, function($a,$b) { if ($a[0] == $b[0]) return 0; return ($a[0] < $b[0]) ? -1 : 1; } );
        return $ret;
    }

    public static function current_skin(?int $time = null): ?string
    {
        $ev = static::current($time);
        if ($ev && file_exists(APPPATH . "assets/skins/$ev"))
            return $ev;
        else return null;
    }

    public static function is_april_fools(): bool
    {
        return (static::get(static::TE_MONTH) === 4 && static::get(static::TE_DAY) === 1) && (Globals::CurrentGameF()->duration() > 300) && !Globals::PrimaryPlayerF()->april_fools();
    }

    public static function is_october_midness(): bool
    {
        return (static::get(static::TE_MONTH) === 10 && static::get(static::TE_DAY) === 14);
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