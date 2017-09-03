<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Places_Camping_Grill extends Model_Places_Abstract_Node {
	
	protected static $name = 'Grillplatz';
	protected static $description = 'Der Grillplatz war einst der wichtigste Ort dieses Campingplatzes; hier wurden Nahrungsmittel und Getränke verteilt sowie soziale Kontakte geschlossen. Jetzt ist der Platz wie ausgestorben, bis auf ein paar herumschleichende Zombies natürlich. Und die haben kein interesse an sozialen Kontakten...';
    protected static $icon = 'grill';
    protected static $outside = true;
    protected static $upgradable = false;

    public function setup_additional_rooms() {
        parent::setup_additional_rooms();

        $this->setup_new_room($this->create_new_room(20,['outside']),
                              ['bbq_grill'],
                              [],
                              "Grillstation");
    }
}	