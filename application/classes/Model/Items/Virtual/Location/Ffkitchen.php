<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Virtual_Location_Ffkitchen extends Model_Items_Abstract_Virtual {

    protected $remaining = array(
        'mp_kitchen' => PHP_INT_MAX
    );

    protected function hid() {
        $phpbb53 = $this;
        return parent::hid()->add_action('Küche ...', Model_Action::factory()
                ->description('Die Küchengeräte hier scheinen in passablem Zustand zu sein. Wie wärs, wenn du etwas kochen würdest?')
                ->javascript(Model_Javascript::factory()
                    ->close_qtip()
                    ->versa('mypos'))
            , 'mp_kitchen');
    }
}	