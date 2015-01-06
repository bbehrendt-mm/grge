<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Places_House extends Model_Places_Abstract_Hideout {

    protected static $name = 'Dein Einfamilienhaus';
    protected static $description = 'Gerade erst hast du die letzte Rate für dein Haus bezahlt, da musst du es wegen der Zombieapokalypse direkt wieder evakuieren. Hättest du doch damals nur diese Zombieversicherung abgeschlossen...';
    protected static $icon = 'home';

    //Base defense
    protected $defense = 35;

    //Base: 15% per day
    protected static $decay_rate = 0;

    //Exp: 8% per day
    protected static $decay_exp = 0;

    public function uin($new = null) {
        if ($new !== null) {
            $this->add_upgrades(['bedr1','bedr2','bedr3','sofa1','sofa2','manu1','manu2','gen1','gen2']);

            $this->home_extensions("kitchen", "base", true);
            $this->home_extensions("kitchen", "boiler", true);
            $this->home_extensions("kitchen", "utils", true);
            $this->home_extensions("kitchen", "oven", true);

            $this->home_extensions("defense", "fence", true);
        }
        return parent::uin($new);
    }

}	