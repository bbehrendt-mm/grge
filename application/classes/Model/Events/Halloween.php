<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Events_Halloween extends Model_Events_Event {

    protected static $event_key = 'halloween';

    protected function trigger_activation() {
        return true;
    }

    protected function trigger_deactivation() {
        return true;
    }

    public function tick() {
        return true;
    }

}