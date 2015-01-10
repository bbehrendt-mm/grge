<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Places_Roadtrip_Myhouse extends Model_Places_Abstract_Hideout {

    protected static $name = 'Dein Einfamilienhaus';
    protected static $description = 'Gerade erst hast du die letzte Rate für dein Haus bezahlt, da musst du es wegen der Zombieapokalypse direkt wieder evakuieren. Hättest du doch damals nur diese Zombieversicherung abgeschlossen...';
    protected static $icon = 'myhouse';

    //Base defense
    protected $defense = 35;

    //Base: 15% per day
    protected static $decay_rate = 0;

    //Exp: 8% per day
    protected static $decay_exp = 0;

    public function uin($new = null) {
        if ($new !== null) {
            $this->add_upgrades('hideout','bedr1','bedr2','bedr3','sofa1','sofa2','manu1','manu2','gen1','gen2','ktc1','ktc2','ktc3','ktc4','deffence1','fence','outside','outside_space');
            $this->set_decay(0, true);
        }
        return parent::uin($new);
    }

}	