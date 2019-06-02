<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Places_Roadtrip_Garage extends Model_Places_Abstract_Place {

    protected static $namelist = Array('"Pay\'n\'Spray" Autowerkstatt', 'Bobs Autobude', '"Zombiewagen" Vertragswerkstatt');
    protected static $description = 'Wenn dein Auto merkwürdige Geräusche macht, hast du entweder einen Motorschaden oder einen Zombie auf der Rückbank. Glücklicherweise findet sich immer eine Werkstatt wie diese in der Nähe, die deine Karre reparieren oder den Zombie fachmännisch (mit einem großen Schraubenschlüssel) entfernen können. Unglücklicherweise hat diese Werkstatt derzeit leider aus unerfindlichen Gründen geschlossen...';
    protected static $icon = 'garage';
    protected static $outside = false;

    /**
     * @param bool $force
     * @param bool $return
     * @return bool|Model_Items_Abstract_Item|null
     * @throws Exception
     */
    public function find_item($force = false, $return = false) {
        // Spawn caravan
        /** @noinspection NotOptimalIfConditionsInspection */
        if (!$return && !$force && Globals::CurrentGame()->setting_mode(11000)
            && !Tool_Scripts::is_npc(Globals::CurrentPlayerF()) && !Globals::CurrentGame()->get_property( "roadtrip.caravan_found", false )
            && Tool_Gambling::random(0.25)
        ) {

            Tool_Scripts::place_new_item(new Model_Items_Generic_Caravan());
            Globals::CurrentGame()->get_property( "roadtrip.caravan_found", true );
        }

        return parent::find_item($force, $return);
    }

}	