<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Places_House extends Model_Places_Abstract_Hideout {

    protected static $name = 'Unbewohntes Einfamilienhaus';
    protected static $description = 'Es scheint, als wäre dieses kurz vor der Zombieapokalypse fertig geworden. Es ist zwar vollständig eingerichtet, aber gewohnt hat hier wohl niemand. Dies könnte der ideale Ort für ein Versteck sein... wenn es nicht gerade der Ort wäre, an dem Zombies zuerst nach dir suchen würden.';
    protected static $icon = 'home';

    //Base defense
    protected $defense = 15;

    //Base: 15% per day
    protected static $decay_rate = 0.10;

    //Exp: 8% per day
    protected static $decay_exp = 0.05;

    public function uin($new = null) {
        if ($new !== null) {
            $this->add_upgrades(['bedr1','bedr2','bedr3','manu1','gen1']);

            $this->home_extensions("kitchen", "base", true);
            $this->home_extensions("kitchen", "boiler", true);

            $this->home_extensions("defense", "fence", true);
        }
        return parent::uin($new);
    }

}	