<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Places_Warehouse extends Model_Places_Abstract_Hideout {

    protected static $name = 'Lagerhaus';
    protected static $description = 'Wie jedes Lagerhaus verfügt auch dieses über einfache Schutzmaßnahmen gegen Diebstahl. Plünderer hat das nicht aufhalten können, aber vielleicht Zombies? Du könntest durchaus versuchen, diesen Ort zu einem Versteck zu machen...';
    protected static $icon = 'home';
    protected static $outside = false;

    //Base defense
    protected $defense = 1;

    //Base: 15% per day
    protected static $decay_rate = 0.10;

    //Exp: 8% per day
    protected static $decay_exp = 0.10;

}	