<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Places_Northpole_Druglab extends Model_Places_Druglab {
	
	protected static $location_name = 'Dr. Cringles Forschungslabor';
	protected static $description = 'Über dem Eingang dieses Labors steht in großen Buchstaben "LPT" geschrieben... was immer das bedeuten mag. Offensichtlich wurde hier Forschung an Rentieren betrieben ...';
    protected static $icon = 'lab';
    protected static $outside = false;

    protected static $temperature_engine = -4;

    public function setup_additional_rooms(): void {
        parent::setup_additional_rooms();
        $this->setup_new_room($this->create_new_room( 5,['inside']),
                              ['rudolph_upgrader'],
                              [],
                              'Operationssaal'
        )->set_default_state();
    }
}	