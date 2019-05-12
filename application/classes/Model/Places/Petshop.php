<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Places_Petshop extends Model_Places_Abstract_Place {
	
	protected static $location_name = 'Zoohandlung';
	protected static $description = 'In dieser Zoohandlung konnte man früher kuschelige Tiere kaufen... inzwischen sind die Käfige jedoch leer. Dafür liegen allerlei menschliche Skelette in der Gegend herum. Wo die wohl herkommen?';
    protected static $icon = 'petshop';
    protected static $outside = false;

	public function uin($uin = NULL) {
		if ($uin === NULL) return parent::uin();
		else $t = parent::uin($uin);

        Tool_Gambling::repeat(1, 3, function() {$this->inventory->add(new Model_Items_Bone());});
        Tool_Gambling::repeat(1, 3, function() {$this->inventory->add(new Model_Items_Bone2());});
        Tool_Gambling::repeat(2, 5, function() {$this->inventory->add(new Model_Items_Generic_Bone3());});

		$this->inventory->add(new Model_Items_Vending(get_class($this),
            'Pettington 40K'
        ));
        return $t;
	}

    public function setup_additional_rooms(): void
    {
        parent::setup_additional_rooms();
        $this->create_new_room(10,['inside'])->set_default_state();
        $this->create_new_room(10,['inside'])->set_default_state();
    }

    protected static $user_rooms_allowed = 1;
    protected static $user_rooms_size = 8;
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
                    ['chance'=> 1, 'value'=> Model_Items_Petfood::cls()],
                    ['chance'=> 1, 'value'=> Model_Items_Petfood2::cls()],
                    ['chance'=> 4, 'value'=> Model_Items_Petfood3::cls()]
                ]);
                $room->inventory()->add( new $cls );
            }
        }
        return $room;
    }
}	