<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Places_Pharmacy extends Model_Places_Abstract_Place {

    protected static $namelist = Array('Apotheke "Hustensaft-Schlürfer"', 'Apotheke "Hypochonders bester Freund"', 'Alte Apotheke', 'Apotheke "Wehwehchen"');
	protected static $description = 'Die Tür dieser Apotheke ist von innen mit einem großen Blasentee-Werbeaufsteller verbarrikadiert. Leider hat das übergroße Schaufenster direkt daneben ausreichend Angriffsfläche für die Zombies geboten. Die Leute, die sich hier versteckt hatten, können mit den gelagerten Medikamenten wohl nichts mehr anfangen, also bedien dich ruhig.';
    protected static $icon = 'pharm';
    protected static $outside = false;
	
	public function uin($uin = NULL) {
        if ($uin !== null) {
            $this->inventory->add(new Model_Items_Vending(get_class($this),
                'MedCo. Pharmacorp.'
            ));
            $this->inventory->add(new Model_Items_Virtual_Location_Pillboxes(false));
        }
        return parent::uin($uin);
	}

    public function setup_additional_rooms(): void
    {
        parent::setup_additional_rooms();
        $this->create_new_room(8,['inside'])->set_default_state();
    }

    protected static $user_rooms_allowed = 1;
    protected static $user_rooms_size = 20;
    public function setup_user_room(): ?Model_Room
    {
        $room = parent::setup_user_room();
        if ($room !== null) {
            $room->name( "Lagerraum", true  );
            $room->add_tag(["inside"]);
            $room->set_default_state();

            $room->add_content('storage');
            for ($i = 0; $i < 15; $i++) {
                $cls = Tool_Gambling::roulette([
                    ['chance'=> 5, 'value'=> Model_Items_Pill::cls()],
                    ['chance'=> 2, 'value'=> Model_Items_Paracetin::cls()],
                    ['chance'=> 2, 'value'=> Model_Items_Paracetoid::cls()],
                    ['chance'=> 2, 'value'=> Model_Items_Paralaxium::cls()],
                    ['chance'=> 1, 'value'=> Model_Items_Twinoid::cls()],
                ]);
                $room->inventory()->add( new $cls );
            }
        }
        return $room;
    }
}
