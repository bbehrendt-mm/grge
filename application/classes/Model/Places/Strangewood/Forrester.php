<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Places_Strangewood_Forrester extends Model_Places_Abstract_Hideout {

    protected static $location_name = 'Haus des Försters';
    protected static $description = 'Der Förster dieses Waldes scheint einige merkwürdige Vorlieben gehabt zu haben ... Die Regale sind voll von in Gläsern eingelegten Körperteilen und Bücher in einer dir fremden Sprache liegen auf dem Boden verstreut. Ein großer Teil des Wohnzimmers wird von einer merkwürdigen Apparatur eingenommen...';
    protected static $icon = 'home';

    //Base deco value
    protected static $base_deco_value = -25;

    //Base defense
    protected static $base_defense = 15;

    protected static $decay_rate = 0.08;

    protected static $decay_exp = 0.04;

    public function uin($new = null) {
        if ($new !== null)
            Model_Blueprints::fast_apply($this, 'upgrades', ['deffence1']);

        return parent::uin($new);
    }

    public function battle_location_type(): string
    {
        return $this->is_outside() ? 'hw19_outside' : 'hw19_inside';
    }

    public function setup_additional_rooms(): void
    {
        $this->create_new_room( 5,['inside'])->set_default_state();

        $this->setup_new_room($this->create_new_room( 10,['inside']),
                              ['bedroom'],
                              ['bedr1', 'bedr2'],
            'Schlafzimmer'
        )->set_default_state();
        $this->setup_new_room($this->create_new_room( 12,['inside']),
                              ['workshop','workshop_hw19_forrester'],
                              [],
            'Werkstatt'
        )->set_default_state();

        $this->create_new_room(20,['outside'])->set_default_state();
    }

}	