<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Places_Store extends Model_Places_Abstract_Place {

    protected static $namelist = Array('Tante Emma Laden', 'Kleiner Discounter', 'Kleiner Markt', 'Mini-Markt', 'Kleines Geschäft', 'Laden');
	protected static $description = 'Dies ist die Gelegenheit für dich, das absolut billigste Zeug aus einer minimalen Auswahl von Gebrauchsgegenständen und Lebensmitteln zu ergattern! Die vernagelten (und schlecht geputzten) Schaufenster lassen jedoch erahnen, dass dieses Geschäft wohl in nächster Zeit nicht mehr öffnen wird. Vor dem Laden steht ein Marktwagen mit der Aufschrift "Vera Loewenhaupt & Söhne", unter dem eine Leiche liegt...';
    protected static $outside = false;
    protected static $icon = 'store';

	public function uin($uin = NULL) {
		if ($uin === NULL) return parent::uin();
		else $t = parent::uin($uin);
		
		$this->inventory->add(new Model_Items_Vending(get_class($this),
            'MicroJam Vendor'
        ));
        $this->inventory->add(new Model_Items_Vending2());
        $this->inventory->add(new Model_Items_Virtual_Location_Market());
		$this->inventory->add(new Model_Items_Body('Vera Loewenhaupt', 'Dies muss wohl die Namensgeberin des Wagens sein, unter dem sie liegt... allerdings keine Spur von ihren Söhnen.'));

        return $t;
    }

    public function setup_additional_rooms(): void
    {
        parent::setup_additional_rooms();
        $this->create_new_room(30,['inside'])->set_default_state();
        $this->create_new_room(10,['inside'])->set_default_state();
        $this->create_new_room( 5,['inside'])->set_default_state();
    }

    protected static $user_rooms_allowed = 1;
    protected static $user_rooms_size = 10;
    public function setup_user_room(): ?Model_Room
    {
        $room = parent::setup_user_room();
        if ($room !== null) {
            $room->name( "Lagerraum", true  );
            $room->add_tag(["inside"]);
            $room->set_default_state();

            $room->add_content('storage');
            for ($i = 0; $i < 5; $i++) {
                $cls = Tool_Gambling::roulette([
                    ['chance'=> 1, 'value'=> Model_Items_Basefood::cls()],
                    ['chance'=> 1, 'value'=> Model_Items_Petfood3::cls()],
                    ['chance'=> 1, 'value'=> Model_Items_Jacket::cls()],
                    ['chance'=> 1, 'value'=> Model_Items_Generic_Sum::cls()],
                    ['chance'=> 1, 'value'=> Model_Items_Wine::cls()],
                    ['chance'=> 1, 'value'=> Model_Items_Beer::cls()],
                    ['chance'=> 1, 'value'=> Model_Items_Generic_Spice::cls()],
                    ['chance'=> 1, 'value'=> Model_Items_Generic_Electro::cls()],
                ]);
                $room->inventory()->add( new $cls );
            }
        }
        return $room;
    }
}	