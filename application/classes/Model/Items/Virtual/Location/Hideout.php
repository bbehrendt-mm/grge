<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Virtual_Location_Hideout extends Model_Items_Abstract_Virtual {

    protected $remaining = array(
        'hideout_builder' => PHP_INT_MAX,
        'hideout_maker' => PHP_INT_MAX,
        'hideout_sleep' => PHP_INT_MAX,
        'hideout_defense' => PHP_INT_MAX,
        'hideout_couch' => PHP_INT_MAX,
    );

    public function __construct($upgradable = true) {
        if (!$upgradable)
            $this->remaining['hideout_builder'] = 0;
    }

    protected function hid() {
        /** @global Model_Player $player */
        global $player;

        //ToDo: ROOOOOOOOOOOMS

        /** @var Model_Places_Abstract_Hideout $location */
        $location = $player->location();
        /** @noinspection PhpUndefinedMethodInspection */
        $location_driving = Tool_System::instance_of($location, 'Model_Places_Motorhome') && $location->is_driving();

        $tmp = parent::hid();

        if ($location_driving) {
            // ToDo: Prevent hideout upgrades here!
            // ToDo: Prevent defense here!
        }

        if (!$location->room()->has_content("hideout_slot"))
            return $tmp;

        return $tmp;
    }
}	