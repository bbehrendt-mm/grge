<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Places_Hotel extends Model_Places_Abstract_Hideout {

    protected static $namelist = Array("Gate's Motel", 'Verfallene Herberge', 'Heruntergekommenes Hotel');
    protected static $description = 'Dieser Ort scheint bereits intensiv geplündert worden zu sein; hier wirst du wohl eher nichts mehr finden. Allerdings ist einer der Gästeräume vergleichsweise gut erhalten. Du könntest hier ein Versteck aufschlagen... wenn du keine Angst davor hast, dass dir die Decke auf den Kopf fällt.';
    protected static $icon = 'home';

    //Base deco value
    protected static $base_deco_value = -25;

    //Base defense
    protected static $base_defense = 12;

    //Base: 10% per day
    protected static $decay_rate = 0.25;

    //Exp: 5% per day
    protected static $decay_exp = 0.02;

    public function setup_additional_rooms(): void
    {
        for ($i = 0; $i < 6; $i++)
            $this->setup_new_room($this->create_new_room(10,['inside']),
                                  ['bedroom'],
                                  ['bedr1','bedr2','bedr3'],
                'Hotelzimmer'
            )->set_default_state();
    }
}	