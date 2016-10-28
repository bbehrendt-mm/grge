<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Virtual_Location_FfHalloweenStore extends Model_Items_Abstract_Virtual {

    protected $remaining = array(
        'mp_hlw' => PHP_INT_MAX
    );

    protected function hid() {
        return parent::hid()->add_action('Beim vermodernden Hängler einkaufen ...', Model_Action::factory()
            ->buttonskin('location')
            ->description('Eigentlich bibt es keinen Grund, nicht bei jemandem einzukaufen, dem das Fleisch in Fetzen herunterhängt.')
            ->popup('maker')
        , 'mp_hlw');
    }
}	