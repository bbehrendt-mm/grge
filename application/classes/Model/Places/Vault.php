<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Places_Vault extends Model_Places_Abstract_Hideout {

    protected static $location_name = 'Verlassener Bunker';
    protected static $description = 'Eigentlich war dieser Bunker dafür gedacht, den Menschen im Falle eines Atomkriegs Schutz zu bieten. Darauf, dass man ihn auch bei der Zombieapokalypse gebrauchen könnte, ist wohl niemand gekommen. Umso besser für dich, denn du kannst diesen Bunker zu einem Versteck machen!';
    protected static $icon = 'home';

    //Base deco value
    protected static $base_deco_value = 0;

    //Base defense
    protected static $base_defense = 30;

    //Base: 15% per day
    protected static $decay_rate = 0.05;

    //Exp: 8% per day
    protected static $decay_exp = 0.01;

    public function setup_additional_rooms(): void
    {
        $this->setup_new_room($this->create_new_room( 5,['inside']),
                              ['utilities'],
                              ['gen1','gen2'],
            'Reaktorraum'
        );
        $this->setup_new_room($this->create_new_room( 8,['inside']),
                              ['bedroom'],
                              ['bedr1'],
            'Schlafzimmer'
        );
        $this->setup_new_room($this->create_new_room( 8,['inside']),
                              ['kitchen'],
                              [],
            'Küche'
        );
        $this->setup_new_room($this->create_new_room( 8,['inside']),
                              ['workshop'],
                              [],
            'Werkstatt'
        );

        $this->create_new_room(8,['inside']);
        $this->create_new_room(8,['inside']);
        $this->create_new_room(8,['inside']);
        $this->create_new_room(8,['inside']);
        $this->create_new_room(8,['inside']);
    }

}	