<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Places_Roadtrip_Roadblock extends Model_Places_Abstract_Place {
	
	protected static $location_name = 'Provisorische Straßenbarrikade';
	protected static $description = 'Hier haben die Menschen anscheinend versucht, die Zombies mithilfe improvisierter Straßenbarrikaden aufzuhalten. So richtig funktioniert hat das wohl aber nicht, immerhin liegen hier überall Leichen herum...';
    protected static $icon = 'barricade';

    public function uin($new = null) {
        if ($new !== null)
            $this->inventory->add(new Model_Items_Virtual_Location_Roadblock());
        return parent::uin($new);
    }
}