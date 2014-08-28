<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Virtual_Location_Ffxmas extends Model_Items_Abstract_Virtual {

    protected $remaining = array(
        'mp_xmas' => PHP_INT_MAX
    );

    protected function hid() {
        $phpbb53 = $this;
        return parent::hid()->add_action('Basteln ...', Model_Action::factory()
                ->javascript(Model_Javascript::factory()
                    ->close_qtip()
                    ->versa('mypos'))
            , 'mp_xmas');
    }
}	