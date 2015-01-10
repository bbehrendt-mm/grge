<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Places_Camping_Caravan extends Model_Places_Abstract_Hideout {
	
	protected static $name = 'Gestrandetes Wohnmobil';
	protected static $description = 'Die Reifen dieses Wohnmobils sind zerstört, der Motor ist beschädigt und Benzin ist auch nicht mehr im Tank. Ich würde sagen, mit diesem Teil fährst du nirgenwo mehr hin; aber häuslich einrichten kannst du dich da drin natürlich trotzdem.';
    protected static $icon = 'home';
    protected static $outside = false;

    //Base defense
    protected $defense = 2;

    //Base: 15% per day
    protected static $decay_rate = 0.30;

    //Exp: 8% per day
    protected static $decay_exp = 0.10;

    public function uin($new = null) {
        if ($new !== null)
            $this->add_upgrades(['bedr1','gen1','ktc1','outside','outside_space']);

        return parent::uin($new);
    }
}	