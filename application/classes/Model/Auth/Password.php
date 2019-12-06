<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Auth_Password extends Model_Auth_Interface {

    protected static $service_name = 'E-Mail/Password';

    private $name;
    private $avatar;

    static private function hash_password( string $password ): ?string {
        $trimmed_pw = trim($password);
        return empty($trimmed_pw) ? null : hash('sha512', $trimmed_pw . '-zv-password-3PHJnuNSQLNp5n');
    }

    public function __construct($email, $password) {
        $pw = static::hash_password( $password );
        $this->zvid = $pw ? static::lookup(null, $email, $pw) : -1;

        if ($this->zvid >= 0) {
            $this->rid = $this->zvid;
            $this->name = Model_Euser::name_by_id($this->zvid, false);
            $this->avatar = Model_Euser::avatar_by_id($this->zvid, false);

            $this->ready = true;
        } else $this->last_error = 'invalid_keys';
    }

    public static function check_email_validity( string $email): bool {
        return (!empty(trim($email))) && preg_match( '/^([^@\\/\\\\\\s])+@([^@\\/\\\\\\s])+\.([^@\\/\\\\\\s])+$/', $email );
    }

    public static function create($id, $email, $password, bool $activate = false): bool {
        // Check
        $pw = static::hash_password( $password );
        if (!$pw || empty($email) || static::lookup(null, $email) >= 0 || static::user_is_connected($id))
            return false;

        if (!static::check_email_validity($email))
            return false;

        static::user_link($id,$activate ? $id : (-42*$id),$email,$pw);
        return true;
    }

    public static function user_is_activated($id): bool {
        return static::user_get_rid( $id ) > 0;
    }

    public static function user_pending_activation($id): bool {
        return static::user_get_rid($id) <= -42;
    }

    public static function user_activation_key($id): ?string {
        if (!static::user_pending_activation($id)) return null;

        $data = static::user_get_values($id);
        if (!$data) return null;

        return substr(md5($data[1]), 0, 6);
    }

    public function getEmail(): ?string {
        if (!$this->is_ready()) return null;
        $data = $this->get_values();
        return $data ? $data[0] : null;
    }

    public static function user_getEmail( $id ): ?string {
        $data = static::user_get_values( $id );
        return $data ? $data[0] : null;
    }

    public static function user_activate( $id ): bool {
        if (!static::user_pending_activation( $id )) return false;
        $data = static::user_get_values( $id );
        if (!$data) return false;

        static::user_link($id,$id,$data[0],$data[1]);
        return true;
    }

    public static function update($id, $password): bool {
        if (!static::user_is_activated($id)) return false;

        // Check
        $pw = static::hash_password( $password );
        if (!$pw || !static::user_is_connected($id))
            return false;

        $data = static::user_get_values( $id );
        if (!$data) return false;

        static::user_link($id,$id,$data[0],$pw);
        return true;
    }

    public function getRemoteName() {
        return Model_Euser::name_by_id($this->zvid, false);
    }

    public function getRemoteAvatarUrl() {
        return Model_Euser::avatar_by_id($this->zvid, false);
    }

    public function connectToLocal($target_id = null): bool
    {
        return $this->zvid > 0;
    }


}