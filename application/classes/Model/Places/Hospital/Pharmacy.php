<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Places_Hospital_Pharmacy extends Model_Places_Abstract_Place {
	
	protected static $location_name = 'Krankenhausapotheke';
	protected static $description = 'Du dachtest, in anderen Apotheken wirst du gut versorgt? Tja, dann bist du wohl noch nie hier gewesen - das Krankenhaus hat bunkert das richtig heftige Zeug! Unglücklicherweise bunkert das krankenhaus auch die richtig heftigen Zombies... schnapp dir also lieber schnell so viel, wie du tragen kannst, und nimm die Beine in die Hand! Wenn du lebensmüde bist kannst du natürlich auch deine Zeit damit verbringen, die verstreuten Pillenschachteln auf dem Boden zu durchsuchen...';
    protected static $outside = false;

    protected static $icon = 'hospital_pharm';

    public function uin($uin = NULL) {
        if ($uin !== null) {
            $this->inventory->add(new Model_Items_Vending(get_class($this), "MedCo. Pharmacorp."));
            $this->inventory->add(new Model_Items_Virtual_Location_Pillboxes(true));
        }
        return parent::uin($uin);
    }
}	