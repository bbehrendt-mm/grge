<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Virtual_Location_Hideout extends Model_Items_Abstract_Virtual {

    protected $remaining = array(
        'hideout_builder' => PHP_INT_MAX,
        'hideout_maker' => PHP_INT_MAX,
        'hideout_sleep' => PHP_INT_MAX,
        'hideout_defense' => PHP_INT_MAX,
        'hideout_couch' => PHP_INT_MAX,
    );

    protected function hid() {
        /** @var Model_Places_Abstract_Hideout $location */
        $location = Globals::CurrentPlayer()->location();
        /** @noinspection PhpUndefinedMethodInspection */
        $location_driving = Tool_System::instance_of($location, 'Model_Places_Motorhome') && $location->is_driving();

        $tmp = parent::hid();

        if ($location_driving) {
            // ToDo: Prevent hideout upgrades here!
            // ToDo: Prevent defense here!
        }

        return $tmp;
    }
}	