<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Places_Hotel extends Model_Places_Abstract_Hideout {

    protected static $namelist = Array("Gate's Motel", 'Verfallene Herberge', 'Heruntergekommenes Hotel');
    protected static $description = 'Dieser Ort scheint bereits intensiv geplündert worden zu sein; hier wirst du wohl eher nichts mehr finden. Allerdings ist einer der Gästeräume vergleichsweise gut erhalten. Du könntest hier ein Versteck aufschlagen... wenn du keine Angst davor hast, dass dir die Decke auf den Kopf fällt.';
    protected static $icon = 'home';

    //Base defense
    protected $defense = 4;

    //Base: 15% per day
    protected static $decay_rate = 0.35;

    //Exp: 8% per day
    protected static $decay_exp = 0.02;

    public function uin($new = null) {
        if ($new !== null)
            $this->add_upgrades(['bedr1','bedr2','bedr3']);

        return parent::uin($new);
    }

}	