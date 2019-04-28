<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Places_Radio extends Model_Places_Abstract_Place {
	
	protected static $location_name = 'Radio Sandstorm Sendestation';
	protected static $description = 'Dies ist die einzige Radiostation, die selbst Wochen nach Ausbruch der Zombieinfektion noch sendete. Unermütlich und rund um die Uhr versuchten die Moderatoren, die Überlebenden in ihrem Sendegebiet zu koordinieren - leider haben sie dabei wohl vergessen, die Tür zu ihrem Studio abzuschließen.';
    protected static $icon = 'radio';
    protected static $outside = false;

    public function setup_additional_rooms(): void
    {
        parent::setup_additional_rooms();
        $this->create_new_room(4,['inside'])->set_default_state();
        $this->create_new_room(4,['inside'])->set_default_state();
        $this->create_new_room(4,['inside'])->set_default_state();
        $this->create_new_room(8,['inside'])->set_default_state();
    }
}	