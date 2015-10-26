<?php defined('SYSPATH') OR die('No direct script access.');

class Named {

    public static function cls() {
        return get_called_class();
    }

}
