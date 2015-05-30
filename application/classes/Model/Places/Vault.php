<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Places_Vault extends Model_Places_Abstract_Hideout {

    protected static $name = "Verlassener Bunker";
    protected static $description = 'Eigentlich war dieser Bunker dafür gedacht, den Menschen im Falle eines Atomkriegs Schutz zu bieten. Darauf, dass man ihn auch bei der Zombieapokalypse gebrauchen könnte, ist wohl niemand gekommen. Umso besser für dich, denn du kannst diesen Bunker zu einem Versteck machen!';
    protected static $icon = 'home';

    //Base deco value
    protected static $base_deco_value = 0;

    //Base defense
    protected $defense = 30;

    //Base: 15% per day
    protected static $decay_rate = 0.05;

    //Exp: 8% per day
    protected static $decay_exp = 0.01;

    public function uin($new = null) {
        if ($new !== null)
            $this->add_upgrades(['bedr1','manu1','gen1','slot_epic']);

        return parent::uin($new);
    }

}	