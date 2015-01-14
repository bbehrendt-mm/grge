<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Virtual_Location_Ffgunsmith extends Model_Items_Abstract_Virtual {

    protected $remaining = array(
        'mp_gunshop' => PHP_INT_MAX
    );

    protected function hid() {
        return parent::hid()->add_action('Hinterzimmer ...', Model_Action::factory()
                ->description('Im Hinterzimmer befinden sich allerlei Werkzeuge, die du zur Produktion von Waffen und Munition verwenden kannst.')
                ->javascript(Model_Javascript::factory()
                    ->versa('maker'))
            , 'mp_gunshop');
    }
}	