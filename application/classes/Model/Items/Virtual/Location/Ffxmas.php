<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Virtual_Location_Ffxmas extends Model_Items_Abstract_Virtual {

    protected static $default_action_uses = array(
        'mp_xmas' => PHP_INT_MAX
    );

    protected function hid(): Model_Hid {
        return parent::hid()->add_action('Basteln ...', Model_Action::factory()
            ->buttonskin('location')
            ->popup('maker')
        , 'mp_xmas');
    }
}	