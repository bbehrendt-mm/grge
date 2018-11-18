<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Places_Warehouse extends Model_Places_Abstract_Hideout {

    protected static $location_name = 'Lagerhaus';
    protected static $description = 'Wie jedes Lagerhaus verfügt auch dieses über einfache Schutzmaßnahmen gegen Diebstahl. Plünderer hat das nicht aufhalten können, aber vielleicht Zombies? Du könntest durchaus versuchen, diesen Ort zu einem Versteck zu machen...';
    protected static $icon = 'home';
    protected static $outside = false;

    //Base deco value
    protected static $base_deco_value = -30;

    //Base defense
    protected static $base_defense = 1;

    //Base: 15% per day
    protected static $decay_rate = 0.10;

    //Exp: 8% per day
    protected static $decay_exp = 0.10;

    public function setup_additional_rooms(): void
    {
        parent::setup_additional_rooms();
        $this->create_new_room(30,['inside']);
        $this->create_new_room(30,['inside']);
        $this->create_new_room(30,['inside']);
    }

}	