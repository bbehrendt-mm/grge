<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Chat {

    private static function send($object, $auto_add_token = true) {
        if ($auto_add_token) $object['token'] = Kohana::$config->load('server.externals.chat.token');
        $context  = stream_context_create(array(
            'http' => array(
                'timeout' => 2,
                'header'  => "Content-type: application/x-www-form-urlencoded",
                'method'  => 'POST',
                'content' => http_build_query($object),
            ),
        ));

        try {
            return json_decode(file_get_contents(Kohana::$config->load('server.externals.chat.url'), false, $context),true);
        } catch (Exception $e) {
            return array('error' => $e->getMessage());
        }

    }

    public static function create_room($name, $public) {
        $ret = static::send(array('operation' => 'mkroom', 'name' => $name, 'public' => $public));
        return (!isset($ret['error']));
    }

    public static function delete_room($name) {
        $ret = static::send(array('operation' => 'rmroom', 'name' => $name));
        return (!isset($ret['error']));
    }

    public static function grant_access($room, $uid, $username) {
        $ret = static::send(array('operation' => 'grant', 'name' => $username, 'uin' => $uid, 'room' => $room));
        return (!isset($ret['error']));
    }

    public static function revoke_access($room, $uid) {
        $ret = static::send(array('operation' => 'revoke', 'uin' => $uid, 'room' => $room));
        return (!isset($ret['error']));
    }

    public static function create_token($uid, $name) {
        $ret = static::send(array('operation' => 'login', 'id' => $uid, 'name' => $name));
        if (isset($ret['error'])) return false;
        return $ret['token'];
    }
}