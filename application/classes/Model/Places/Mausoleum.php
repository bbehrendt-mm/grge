<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Places_Mausoleum extends Model_Places_Abstract_Place {
	
	protected static $location_name = 'Mausoleum';
	protected static $description = 'Ob du es glaubst oder nicht - früher sind die meisten Leute nach dem Tod nicht wieder aufgestanden und haben Gehirne gefuttert! Daher brachte man Verstorbene an einen Ort wie diesen, wo sie in Frieden auf ewig ruhen können. Obwohl es heute allein schon wegen der Lebensgefahr nicht mehr üblich ist, Mausoleen zu besuchen, scheinen die Zombies von diesem Ort magisch angezogen zu werden...';
    protected static $icon = 'crypt';
    protected static $outside = false;

	public function uin($uin = NULL) {
		if ($uin === NULL) return parent::uin();
		else $t = parent::uin($uin);

		$this->inventory->add(new Model_Items_Vending(get_class($this),
            'Kill-it-Yourself Coffin Dispenser'
        ));
        return $t;
	}
}	