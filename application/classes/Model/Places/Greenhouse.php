<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Places_Greenhouse extends Model_Places_Abstract_Place {
	
	protected static $location_name = 'Gewächshaus "Plants & Zombies"';
	protected static $description = 'Die Scheiben um dieses Gewächshauses sind allesamt zersprungen, die meisten Pflanzen sind infolge dessen vertrocknet. Im Zentrum des Gewächshauses steht, von einem kleinen Weg umschlossen, ein riesiges baumartiges Gewächs. Obwohl es wie der Rest der Pflanzen hier ziemlich vertrocknet ist, sieht es irgendwie noch lebendig aus... Vielleicht kannst du es zum Leben erwecken, wenn du es gießt?';
    protected static $icon = 'green';

    public function uin($uin = NULL) {
        if ($uin !== null) {
            $this->inventory->add(new Model_Items_Virtual_Location_Greenhouse());
            $this->inventory->add(new Model_Items_Virtual_Epic_Garden());
        }


        return parent::uin($uin);
    }

    protected static $user_rooms_allowed = 1;
    protected static $user_rooms_size = 6;
    public function setup_user_room(): ?Model_Room
    {
        $room = parent::setup_user_room();
        if ($room !== null) {
            $room->name( "Lagerraum", true  );
            $room->add_tag(["inside"]);
            $room->set_default_state();

            $room->add_content('storage');
            for ($i = 0; $i < 10; $i++) {
                $cls = Tool_Gambling::roulette([
                    ['chance'=> 1, 'value'=> Model_Items_Vedge::cls()],
                    ['chance'=> 2, 'value'=> Model_Items_Vedge2::cls()],
                    ['chance'=> 3, 'value'=> Model_Items_Vedge3::cls()],
                    ['chance'=> 4, 'value'=> Model_Items_Vedge4::cls()],
                    ['chance'=> 1, 'value'=> Model_Items_Vedge5::cls()],
                ]);
                $room->inventory()->add( new $cls );
            }
        }
        return $room;
    }

}	