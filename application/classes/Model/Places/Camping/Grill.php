<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Places_Camping_Grill extends Model_Places_Abstract_Node {
	
	protected static $name = 'Grillplatz';
	protected static $description = 'Der Grillplatz war einst der wichtigste Ort dieses Campingplatzes; hier wurden Nahrungsmittel und Getränke verteilt sowie soziale Kontakte geschlossen. Jetzt ist der Platz wie ausgestorben, bis auf ein paar herumschleichende Zombies natürlich. Und die haben kein interesse an sozialen Kontakten...';
    protected static $icon = 'grill';
    protected static $outside = true;

    public function uin($new = null) {
        if ($new !== null) {
            $this->inventory->add(new Model_Items_Virtual_Location_Ffgrill());
            $this->add_upgrades('ktc_grill');
        }

        return parent::uin($new);
    }
}	