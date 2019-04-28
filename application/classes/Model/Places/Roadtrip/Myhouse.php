<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Places_Roadtrip_Myhouse extends Model_Places_Abstract_Hideout {

    protected static $location_name = 'Dein Einfamilienhaus';
    protected static $description = 'Gerade erst hast du die letzte Rate für dein Haus bezahlt, da musst du es wegen der Zombieapokalypse direkt wieder evakuieren. Hättest du doch damals nur diese Zombieversicherung abgeschlossen...';
    protected static $icon = 'myhouse';

    //Base defense
    protected static $base_defense = 35;

    //Base: 15% per day
    protected static $decay_rate = 0;

    //Exp: 8% per day
    protected static $decay_exp = 0;

    public function setup_primary_rooms(): Model_Room {
        $room = parent::setup_primary_rooms();
        Model_Blueprints::fast_apply($this, 'upgrades', ['hideout'], $room);

        return $room;
    }


    public function setup_additional_rooms(): void  {
        $this->setup_new_room($this->room(),
                              [],
                              ['deffence1','fence']);
        $this->setup_new_room($this->create_new_room( 10,['inside']),
                              ['utilities'],
                              ['gen1','gen2'],
            'Keller'
        )->set_default_state();
        $this->setup_new_room($this->create_new_room( 10,['inside']),
                              ['bedroom'],
                              ['bedr1','bedr2','bedr3'],
            'Schlafzimmer'
        )->set_default_state();
        $this->setup_new_room($this->create_new_room( 15,['inside']),
                              ['community'],
                              ['sofa1','sofa2'],
            'Stube'
        )->set_default_state();
        $this->setup_new_room($this->create_new_room( 12,['inside']),
                              ['kitchen'],
                              ['ktc2','ktc3','ktc4'],
            'Küche'
        )->set_default_state();
        $this->setup_new_room($this->create_new_room( 7,['inside']),
                              ['workshop'],
                              [],
            'Garage'
        )->set_default_state();

        $this->create_new_room(10,['inside'])->set_default_state();
    }
}	