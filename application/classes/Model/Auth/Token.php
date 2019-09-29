<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Auth_Token extends Model_Auth_Interface {

    protected static $service_name = 'ZV-Token';

    private $name;
    private $avatar;

    public function __construct($token) {
        $this->zvid = static::lookup(null, $token);

        if ($this->zvid >= 0) {
            $this->rid = $this->zvid;
            $this->name = Model_Euser::name_by_id($this->zvid, true);
            $this->avatar = Model_Euser::avatar_by_id($this->zvid, true);

            $this->ready = true;
        } else $this->last_error = 'invalid_keys';
    }

    public static function token($id) {
        if (static::user_is_connected($id))
            return static::user_get_values($id)[0];
        else {
            $token = hash('sha512', $id . mt_rand() . microtime());
            static::user_link($id,$id,$token);
            return $token;
        }
    }

    public function getRemoteName() {
        return Model_Euser::name_by_id($this->zvid, true);
    }

    public function getRemoteAvatarUrl() {
        return Model_Euser::avatar_by_id($this->zvid, true);
    }

    public function connectToLocal($target_id = null): bool
    {
        return $this->zvid > 0;
    }
}