<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Virtual_Location_Place extends Model_Items_Abstract_Virtual {

    protected $remaining = array(
        'show_rooms' => PHP_INT_MAX,
    );


    protected function hid() {
        /** @global Model_Player $player */
        global $player;

        //ToDo: ROOOOOOOOOOOMS

        return parent::hid()->add_action('Dieser Ort ...', Model_Action::factory()
            ->buttonskin('hideout')
            ->description('Hier kannst du sehen, was es an diesem Ort so zu tun gibt. Möglicherweise kannst du sogar ein paar Ausbauten vornehmen...')
            ->popup('rooms')
            , 'show_rooms');
    }
}	