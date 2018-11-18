<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Places_House extends Model_Places_Abstract_Hideout {

    protected static $location_name = 'Unbewohntes Einfamilienhaus';
    protected static $description = 'Es scheint, als wäre dieses kurz vor der Zombieapokalypse fertig geworden. Es ist zwar vollständig eingerichtet, aber gewohnt hat hier wohl niemand. Dies könnte der ideale Ort für ein Versteck sein... wenn es nicht gerade der Ort wäre, an dem Zombies zuerst nach dir suchen würden.';
    protected static $icon = 'home';

    //Base deco value
    protected static $base_deco_value = 5;

    //Base defense
    protected static $base_defense = 15;

    //Base: 15% per day
    protected static $decay_rate = 0.10;

    //Exp: 8% per day
    protected static $decay_exp = 0.05;

    public function uin($new = null) {
        if ($new !== null)
            Model_Blueprints::fast_apply($this, 'upgrades', ['deffence1']);

        return parent::uin($new);
    }

    public function setup_additional_rooms(): void
    {
        $this->create_new_room( 5,['inside']);

        $this->setup_new_room($this->create_new_room( 5,['inside']),
                              ['utilities'],
                              ['gen1'],
            'Keller'
        );
        $this->setup_new_room($this->create_new_room( 10,['inside']),
                              ['bedroom'],
                              ['bedr1', 'bedr2', 'bedr3'],
            'Schlafzimmer'
        );
        $this->setup_new_room($this->create_new_room( 12,['inside']),
                              ['kitchen'],
                              ['ktc2', 'ktc3', 'ktc4'],
            'Küche'
        );
        $this->setup_new_room($this->create_new_room( 12,['inside']),
                              ['workshop'],
                              [],
            'Werkstatt'
        );

        $this->create_new_room(15,['inside']);
    }

}	