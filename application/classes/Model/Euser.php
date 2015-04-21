<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Euser extends Model_User {

    private static $cache = [];

    public function __sleep() {
        return array('set', 'sid');
    }

    public function __wakeup() {
        //Rebind global user variable
        global $user;
        $user = $this;
    }

    public function __construct($session_id) {
        //Bind global user variable
        global $user;
        $user = $this;

        //Save session ID
        $this->sid = $session_id;
    }

    public static function register($name, $avatar) {
        list($insert_id, $affected_rows) = DB::insert('users', array('name', 'avatar'))->values(array($name, $avatar))->execute();
        return $insert_id;
    }

    /**
     * Loads lockout data into cache
     */
    private function cache_lockouts() {
        if (isset(static::$cache['lockout']))
            return false;

        $lockouts = DB::select(array(DB::expr('MIN(`timestamp`)'), 'min_time'), array(DB::expr('MAX(`timestamp`)'), 'max_time'), array(DB::expr('COUNT(`timestamp`)'), 'count'))->from('mp_lockouts')->where('uid', '=', $this->uid())->and_where('timestamp', '>', strtotime('-' . Kohana::$config->load('basic.multiplayer.mp_lockouts.time')))->group_by('uid')->execute()->as_array();
        if (count($lockouts) > 0) {
            static::$cache['lockout'] = $lockouts[0];
            static::$cache['lockout']['locked'] = (static::$cache['lockout']['count'] > Kohana::$config->load('basic.multiplayer.mp_lockouts.max_count'));
        } else static::$cache['lockout'] = array('max_time' => 0, 'min_time' => 0, 'count' => 0, 'locked' => false);

        return true;
    }

    /**
     * Returns true if this user is currently blocked from joining public multiplayer games
     * @return boolean
     */
    public function lockouts_is_locked() {
        $this->cache_lockouts();
        return static::$cache['lockout']['locked'];
    }

    /**
     * Returns an array containing the time until the next and last complaints will be negated. The first timestamp contains the time for the last (oldest) complaint, the second for the next (latest) complaint. If the user does not have any active complaints, both values will be 0.
     * @return int[]
     */
    public function lockouts_get_time_range() {
        $this->cache_lockouts();
        return [static::$cache['lockout']['min_time'],static::$cache['lockout']['max_time']];
    }

    /**
     * Returns the number of active complaints for this user
     * @return int
     */
    public function lockouts_get_count() {
        $this->cache_lockouts();
        return static::$cache['lockout']['count'];
    }

    public static function user_update_avatar($id, $url) {
        if (!$url) $url = null;
        DB::update('users')->set(['avatar' => $url])->where('uid', '=', $id)->execute();
    }

    public function update_avatar($url) {
        static::user_update_avatar($this->uid(), $url);
    }
}
