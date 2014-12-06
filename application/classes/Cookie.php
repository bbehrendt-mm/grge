<?php defined('SYSPATH') OR die('No direct access allowed.');

class Cookie extends Kohana_Cookie {
	public static $salt = 'b6c3bc89d7a57dcf5a0f9436b6882decc87263b7';
	public static $expiration = Date::DAY;
	public static $httponly = false;
}