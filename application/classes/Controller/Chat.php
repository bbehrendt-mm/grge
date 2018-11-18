<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Chat extends Controller {

    public const CC_IGNORE = 0;
    public const CC_PING = 1;
    public const CC_MESSAGE = 2;
    public const CC_WHISPER = 3;
    public const CC_STATE = 4;
    public const CC_AUTH = 5;
    public const CC_PIN = 6;

    public const CC_VAR_UNKNOWN= '::?';
    public const CC_VAR_SYSTEM = '::SYSTEM';
    public const CC_VAR_MODERATOR = '::MOD';

    protected static $initialize_session = false;
    protected static $force_ajax = true;

    public static function tokenize($user_id, $room_id, $require_registration) {
        return ($require_registration && !static::check_registration($user_id, $room_id)) ? null : Gateway\encrypt([$user_id,$room_id]);
    }

    private static function detokenize($token) {
        if (!$token) return null;
        return Gateway\decrypt($token);
    }

    public static function register_user($user_id, $room_id): void
    {
        static::push($room_id, $user_id, -1, static::CC_AUTH);
        static::push($room_id, $user_id, -1, static::CC_PING);
    }

    public static function check_registration($user_id, $room_id): bool
    {
        return (bool)DB::select([DB::expr('COUNT(`mid`)'),'c'])->from('chat')->where('type','=',static::CC_AUTH)->where('sender','=',$user_id)->execute()->get('c', 0);
    }

    public static function update_registration($user_id, $room_id): bool
    {
        return (bool)DB::update('chat')->set(['timestamp' => time()])->where('room','=',$room_id)->where('type','=',static::CC_PING)->where('sender','=',$user_id)->execute();
    }

    public static function revoke_registration($user_id, $room_id): bool
    {
        return (bool)DB::delete('chat')->where('room','=',$room_id)->where('type','=',static::CC_AUTH)->where('sender','=',$user_id)->execute();
    }

    public static function purge_room($room_id): bool
    {
        return (bool)DB::delete('chat')->where('room','=',$room_id)->execute();
    }

    private function count_messages($user, $timespan): int
    {
        return (int)DB::select([DB::expr('COUNT(`mid`)'),'c'])->from('chat')->where('timestamp','>',time() - $timespan)->where('sender','=',$user)->execute()->get('c', 0);
    }

    private static function push($room, $user = -1, $receiver = -1, $type = null, $data = null) {
        if ($type === null) $type = static::CC_PING;
        return DB::insert('chat', ['room','sender','receiver','type','message','timestamp'])->values([$room,$user,$receiver,$type,$data === null ? null : serialize($data), time()])->execute();
    }

    private function users($room) {
        $u = DB::select('tchat.sender','tchat.timestamp','users.name')
            ->from('users')
            ->join([DB::select('sender','timestamp')->from('chat')->where('room','=',$room)->where('type','=',static::CC_PING),'tchat'],'RIGHT')->on('tchat.sender','=','users.uid')
            ->execute()->as_array('sender');

        foreach ($u as &$entry)
            $entry = [$entry['name'],$entry['timestamp']];
        return $u;
    }

    private function format($data,$user,$users): array {
        $d_out = [];

        foreach ($data as $mid => $entry) {
            $sender_id = $entry['sender'];

            if ($entry['sender'] === -1) $entry['sender'] = static::CC_VAR_SYSTEM;
            elseif ($entry['sender'] === -2) $entry['sender'] = static::CC_VAR_MODERATOR;
            elseif (isset($users[$entry['sender']][0])) $entry['sender'] = $users[$entry['sender']][0];
            else $entry['sender'] = static::CC_VAR_UNKNOWN;

            $entry['message'] = ($entry['message'] === null) ? null : @unserialize($entry['message'], ['allowed_classes' => false]);

            switch ($entry['type']) {
                case static::CC_IGNORE:case static::CC_PING:case static::CC_AUTH:
                    break;
                case static::CC_PIN:
                    if ($entry['message'])
                        $d_out[$mid] = ['type' => $entry['type'], 'message' => $entry['message']];
                    break;
                case static::CC_MESSAGE: case static::CC_STATE:
                    if ($entry['message'])
                        $d_out[$mid] = ['type' => $entry['type'], 'sender' => $entry['sender'], 'message' => $entry['message'], 'timestamp' => $entry['timestamp']];
                    break;
                case static::CC_WHISPER:
                    if ($entry['message'] && ($entry['receiver'] === $user || $sender_id === $user))
                        $d_out[$mid] = ['type'           => $entry['type'], 'sender' => $entry['sender'], 'to' => $sender_id === $user ? ($users[$entry['receiver']][0]
                            ?? '???') : false, 'message' => $entry['message'], 'timestamp' => $entry['timestamp']];
                    break;
                default: break;
            }
        }

        return $d_out;
    }

    private function transmute($msg, $user, $room, $users) {
        if ($msg === '') return false;
        $msg = mb_substr($msg, 0, 512);

        if ($this->count_messages($user,180) >= 50) return false;

        $command = '/msg';
        $message = $msg;
        $receiver = -1;

        if (strpos($msg, '/') === 0) {
            $tmp_m = explode(' ', $msg, 2);
            if (count($tmp_m) < 2) $tmp_m[1] = '';
            [$command,$message] = $tmp_m;
            $msg = $message;
        }

        $command = mb_strtolower($command);

        if ($command === '/whisper')  {
            if (!$msg) return false;
            $tmp_m = explode(' ', $msg, 2);
            if (count($tmp_m) < 2) return false;
            [$rec,$message] = $tmp_m;
            foreach ($users as $uid => $entry)
                if (mb_strtolower($entry[0]) === mb_strtolower($rec))
                    $receiver = $uid;
            if ($receiver === -1) return false;
        }

        switch ($command) {
            case '/msg':
                return static::push($room,$user,-1,static::CC_MESSAGE,$message);
            case '/pin':
                return static::push($room,$user,-1,static::CC_PIN,$message);
            case '/whisper':
                return static::push($room,$user,$receiver,static::CC_WHISPER,$message);
            case '/me':
                return static::push($room,$user,-1,static::CC_STATE,$message);
            default: return false;
        }
    }

    public function japi_default() {
        if (!($ses = static::detokenize(self::post('t')))) return null;
        [$user, $game] = $ses;
        if (!static::check_registration($user, $game)) return null;
        static::update_registration($user, $game);

        $users = $this->users($game);
        $this->transmute(self::post('m'),$user,$game,$users);

        $last = (int)self::post('l');
        if ($last === 0)
            $result = DB::select('mid','type','message','timestamp','sender','receiver')->from('chat')->where('room','=',$game)->and_where_open()->where('timestamp','>',time() - 1800)->or_where('type','=',static::CC_PIN)->and_where_close()->execute()->as_array('mid');
        else $result = DB::select('mid','type','message','timestamp','sender','receiver')->from('chat')->where('room','=',$game)->where('mid','>',$last)->where('timestamp','>',time() - 1800)->execute()->as_array('mid');

        return $this->render([
            'p' => $this->format($result,$user,$users),
            'u' => array_map(function($a) {return [$a[0],$a[1] > (time() - 20)];}, $users)
        ]);
    }
}