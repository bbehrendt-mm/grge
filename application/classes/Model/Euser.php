<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Euser extends Model_User {

    private static $cache = [];

    public function __sleep() {
        return array('set', 'sid');
    }

    public function __wakeup() {
        //Rebind global user variable
        Globals::setCurrentUser($this);
    }

    public function __construct($session_id) {
        //Bind global user variable
        Globals::setCurrentUser($this);

        //Save session ID
        $this->sid = $session_id;
    }

    public static function register($name, $avatar) {
        [$insert_id, ] = DB::insert('users', array('name', 'avatar'))->values(array($name, $avatar))->execute();
        static::create_profile_data_if_missing($insert_id, true);
        return $insert_id;
    }

    public static function create_profile_data_if_missing( int $uid, bool $force = false ) {
        $ex = 0;
        if (!$force) [$ex,] = DB::select(array(DB::expr('COUNT(*)'), 'num'))->from('user_data')->where('user', '=', $uid)->execute()->as_array(null,'num');
        if ($force || (int)$ex === 0) DB::insert( 'user_data', ['user','access'] )->values(array($uid, md5(':' . $uid . 'this_is_random')))->execute();
    }

    public static function update_name_override_by_id( int $id, ?string $new_name = null ): void {
        DB::update('user_data' )->set(['local_name' => $new_name])->where('user', '=', $id)->execute();
    }

    public static function update_avatar_override_by_id( int $id, ?string $avatar = null, ?string $format = null ): void {
        if ($avatar === null || $format === null)
            $avatar = $format = null;
        DB::update('user_data' )->set(['local_avatar' => $avatar, 'local_avatar_name' => $format ? (time() . ".{$format}") : null])->where('user', '=', $id)->execute();
    }

    public function update_name_override( ?string $new_name = null ): void {
        static::update_name_override_by_id( $this->uid() );
        $this->read( $this->uid() );
    }

    public function update_avatar_override( ?string $avatar = null, ?string $format = null  ): void {
        static::update_avatar_override_by_id( $this->uid(), $avatar, $format );
        $this->read( $this->uid() );
    }

    public function get_access_code_by_id(int $user): string {
        $d = DB::select('access')->from('user_data')->where('user', '=', $user)->execute()->as_array(null,'access');
        return sizeof($d) === 1 ? $d[0] : null;
    }

    public function get_access_code() {
        return $this->set['access'];
    }

    public static function update_by_id(int $id, ?string $name = null, ?string $avatar = null): void {
        if ($name === null && $avatar === null) return;
        if ($avatar !== null) DB::update('users')->set(['avatar' => $avatar])->where('uid', '=', $id)->execute();
        if ($name !== null) static::update_by_id( $id, $name );
    }

    public function update(?string $name = null, ?string $avatar = null): void {
        static::update_by_id( $this->uid(), $name, $avatar );
        $this->read( $this->uid() );
    }

    public static function remove_avatar_by_id($id) {
        DB::update('users')->set(['avatar' => null])->where('uid', '=', $id)->execute();
    }

    public function remove_avatar() {
        static::remove_avatar_by_id( $this->uid() );
    }

    /**
     * Loads lockout data into cache
     */
    private function cache_lockouts(): bool
    {
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
    public function lockouts_is_locked(): bool
    {
        $this->cache_lockouts();
        return static::$cache['lockout']['locked'];
    }

    /**
     * Returns an array containing the time until the next and last complaints will be negated. The first timestamp contains the time for the last (oldest) complaint, the second for the next (latest) complaint. If the user does not have any active complaints, both values will be 0.
     * @return int[]
     */
    public function lockouts_get_time_range(): array
    {
        $this->cache_lockouts();
        return [static::$cache['lockout']['min_time'],static::$cache['lockout']['max_time']];
    }

    /**
     * Returns the number of active complaints for this user
     * @return int
     */
    public function lockouts_get_count(): int
    {
        $this->cache_lockouts();
        return static::$cache['lockout']['count'];
    }
}
