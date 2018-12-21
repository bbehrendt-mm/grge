<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Buffs_Drunk2 extends Model_Buffs_Abstract_Fragile {

    protected static $name = 'Torkeln';
    protected static $desc = 'Du bist aktuell mit dem Bewältigen deiner alkoholbedingten Gleichgewichtsprobleme beschäftigt.';
    protected static $icon = 'tumble_drunk';

    protected function action_on_complete() {}
}