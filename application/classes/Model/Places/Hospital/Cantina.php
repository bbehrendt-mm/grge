<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Places_Hospital_Cantina extends Model_Places_Abstract_Place {
	
	protected static $location_name = 'Krankenhauskantine';
	protected static $description = 'Diese Krankenhauskantine war früher mal ein echter Geheimtipp in der Stadt, denn hier konnte man vergleichsweise billig eine leckere Mahlzeit bekommen. Der günstige Preis ist auch kein Wunder - man kann Nahrungsmittel ziemlich billig produzieren, wenn sich im selben Gebäude eine Leichenhalle befindet...';
    protected static $icon = 'restaurant';
    protected static $outside = false;

    public function uin($new = null) {
        if ($new !== null) {
            $this->inventory->add(new Model_Items_Vending('gp_vdrinks', 'Cholera Cola'));
            $this->inventory->add(new Model_Items_Vending('gp_vfood', 'OmNomNutria'));
        }
        return parent::uin($new);
    }

}	