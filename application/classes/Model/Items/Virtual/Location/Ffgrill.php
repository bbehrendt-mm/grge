<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Virtual_Location_Ffgrill extends Model_Items_Abstract_Virtual {

    protected $remaining = array(
        'mp_grill' => PHP_INT_MAX
    );

    protected function hid() {
        return parent::hid()->add_action('Grillen ...', Model_Action::factory()
            ->buttonskin('location')
            ->popup('maker')
        , 'mp_grill');
    }
}	