<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Places_Camping_Caravan extends Model_Places_Abstract_Hideout {
	
	protected static $location_name = 'Gestrandetes Wohnmobil';
	protected static $description = 'Die Reifen dieses Wohnmobils sind zerstört, der Motor ist beschädigt und Benzin ist auch nicht mehr im Tank. Ich würde sagen, mit diesem Teil fährst du nirgenwo mehr hin; aber häuslich einrichten kannst du dich da drin natürlich trotzdem.';
    protected static $icon = 'home';
    protected static $outside = false;

    //Base defense
    protected $defense = 2;

    //Base: 15% per day
    protected static $decay_rate = 0.30;

    //Exp: 8% per day
    protected static $decay_exp = 0.10;

    public function setup_additional_rooms() {
        $this->create_new_room( 5,['inside']);

        $this->setup_new_room($this->create_new_room( 5,['inside']),
                              ['utilities'],
                              ['gen1']);
        $this->setup_new_room($this->create_new_room( 5,['inside']),
                              ['bedroom'],
                              ['bedr1'],
                              "Schlafzimmer");
        $this->setup_new_room($this->create_new_room( 5,['inside']),
                              ['kitchen'],
                              [],
                              "Küche");

        $this->create_new_room(15,['outside']);
    }
}	