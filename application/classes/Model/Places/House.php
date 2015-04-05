<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Places_House extends Model_Places_Abstract_Hideout {

    protected static $name = 'Unbewohntes Einfamilienhaus';
    protected static $description = 'Es scheint, als wäre dieses kurz vor der Zombieapokalypse fertig geworden. Es ist zwar vollständig eingerichtet, aber gewohnt hat hier wohl niemand. Dies könnte der ideale Ort für ein Versteck sein... wenn es nicht gerade der Ort wäre, an dem Zombies zuerst nach dir suchen würden.';
    protected static $icon = 'home';

    //Base deco value
    protected static $base_deco_value = 5;

    //Base defense
    protected $defense = 15;

    //Base: 15% per day
    protected static $decay_rate = 0.10;

    //Exp: 8% per day
    protected static $decay_exp = 0.05;

    public function uin($new = null) {
        if ($new !== null)
            $this->add_upgrades(['bedr1','bedr2','bedr3','manu1','gen1','ktc1','ktc2','deffence1','fence','outside','outside_space']);

        return parent::uin($new);
    }

}	