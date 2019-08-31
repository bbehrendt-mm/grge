<?php defined('SYSPATH') OR die('No direct access allowed.');

abstract class Model_Auth_Interface {

    protected static $service_name;

    protected $ready = false;
    protected $rid = -1;
    protected $zvid = -1;
    protected $last_error;

    abstract public function connectToLocal($target_id = null): bool;
    abstract public function getRemoteName();
    abstract public function getRemoteAvatarUrl();

    public static function get_service_name() {
        return static::$service_name;
    }

    public function getLocalID(): int
    {
        return $this->zvid;
    }

    /**
     * Last error
     */
    public function getLastError() {
        return $this->last_error;
    }

    public static function get_all_providers($id) {
        return DB::select('provider','rid','var1','var2')->from('profiles_xref')->where('zvid','=',$id)->execute()->as_array('provider');
    }

    /**
     * Returns true if the user has an authentication method connected to his account via this provider
     * @param int $id ZV User ID
     * @return bool True, if the account method is connected
     */
    public static function user_is_connected($id): bool {
        return $id <= 0 ? false : DB::select(array(DB::expr('COUNT(*)'), 'num'))->from('profiles_xref')->where('provider','=', static::class)->where('zvid','=',$id)->execute()->get('num',0) === '1';
    }

    /**
     * Returns true if the user has an authentication method connectd to his account via this provider
     * @return bool True, if the account method is connected
     */
    public function is_connected(): bool
    {
        return static::user_is_connected($this->zvid);
    }

    protected static function lookup($rid = null, $v1 = null, $v2 = null): int
    {
        if ($rid === null && $v1 === null && $v2 === null) return -1;
        $query = DB::select('zvid')->from('profiles_xref')->where('provider','=', static::class);
        if ($rid !== null) $query->where('rid','=',$rid);
        if ($v1 !== null) $query->where('var1','=',$v1);
        if ($v2 !== null) $query->where('var2','=',$v2);

        return (int)$query->execute()->get('zvid',-1);
    }

    protected static function user_get_rid( $id ) {
        if ($id <= 0) return -1;
        $query = DB::select('rid')->from('profiles_xref')->where('provider','=', static::class)->where('zvid','=',$id);
        return (int)$query->execute()->get('rid',-1);
    }

    /**
     * Removes this authentication method from a user
     * @param int $id ZV User ID
     */
    public static function user_unlink($id): void
    {
        DB::delete('profiles_xref')->where('provider','=', static::class)->where('zvid','=',$id)->execute();
    }

    /**
     * Removes all authentication methods from a user
     * @param int $id ZV User ID
     */
    public static function user_unlink_all($id): void
    {
        DB::delete('profiles_xref')->where('zvid','=',$id)->execute();
    }

    /**
     * Removes this authentication method from a user
     */
    public function unlink(): void
    {
        static::user_unlink($this->zvid);
    }

    /**
     * Returns the two value fields, or false when there are none
     * @return array|bool
     */
    protected function get_values() {
        if (!$this->is_ready()) return false;
        return static::user_get_values($this->zvid);
    }

    /**
     * Returns the two value fields, or false when there are none
     * @param int $id ZV User ID
     * @return array|bool
     */
    protected static function user_get_values($id) {
        if (!static::user_is_connected($id)) return false;
        $tmp = DB::select('var1','var2')->from('profiles_xref')->where('provider','=', static::class)->where('zvid','=',$id)->execute()->as_array();
        return [$tmp[0]['var1'],$tmp[0]['var2']];
    }

    /**
     * Adds an authentication method for this user
     * @param null|string $v1
     * @param null|string $v2
     * @return bool
     * @throws Kohana_Exception
     */
    protected function link($v1 = null, $v2 = null): bool
    {
        if (!$this->is_ready()) return false;
        return static::user_link($this->zvid, $this->rid, $v1, $v2);
    }

    /**
     * Adds an authentication method for this user
     * @param int $zvid
     * @param int $rid
     * @param null|string $v1
     * @param null|string $v2
     * @return bool
     * @throws Kohana_Exception
     */
    protected static function user_link($zvid, $rid, $v1 = null, $v2 = null): bool
    {
        if (static::user_is_connected($zvid))
            DB::update('profiles_xref')->set(array('rid' => $rid, 'var1' => $v1, 'var2' => $v2))->where('provider','=', static::class)->where('zvid','=',$zvid)->execute();
        else DB::insert('profiles_xref', array('provider', 'rid', 'zvid', 'var1', 'var2'))->values(array(static::class, $rid, $zvid, $v1, $v2))->execute();
        return true;
    }

    /**
     * Checks if this authenticator is ready
     * @return bool
     */
    public function is_ready(): bool
    {
        return $this->ready && $this->zvid > 0 && $this->rid > 0;
    }
}