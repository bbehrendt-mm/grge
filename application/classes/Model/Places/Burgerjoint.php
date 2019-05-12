<?php defined('SYSPATH') OR die('No direct access allowed.');

//This is a generic building class that has multiple names; on is randomly selected when this class is constructed
class Model_Places_Burgerjoint extends Model_Places_Abstract_Place {
	
	protected static $namelist = Array('"MacUndead" Restaurant', '"ZombieKing" Restaurant', '"Kentucky Fried Eyeballs" Restaurant', '"Ostsee" Restaurant', '"Corpseland" Restaurant', '"Bloodway" Restaurant');
	protected static $description = 'Dieses Franchise hat schonmal bessere Zeiten erlebt ... Die dicken Kinder, die sich hier früher Essensschlachten geliefert haben sind schon lange verschwunden - dafür schleichen nun Zombies zwischen den Tischen und in der Küche umher. Die gute Nachricht ist: Selbst ein Zombie würde das Zeug, das hier serviert wurde, nicht anrühren - du kannst hier also bestimmt noch ein paar Combomenüs abstauben.';
    protected static $icon = 'restaurant';
    protected static $outside = false;

    public function setup_additional_rooms(): void
    {
        parent::setup_additional_rooms();

        $this->setup_new_room($this->create_new_room(10,['inside']),
                              ['kitchen','kitchen_burgerjoint'],
                              [],
            'Küchenbereich'
        )->set_default_state();
        $this->setup_new_room($this->create_new_room(10,['inside']),
                              ['cooler_closed'],
                              [],
            'Kühlkammer'
        );

        $this->create_new_room(20,['inside'])->set_default_state();;
    }

    protected static $user_rooms_allowed = 1;
    protected static $user_rooms_size = 15;
    public function setup_user_room(): ?Model_Room
    {
        $room = parent::setup_user_room();
        if ($room !== null) {
            $room->name( "Spielplatz-Plastikschloss", true  );
            $room->add_tag(["inside"]);
            $room->set_default_state();
        }
        return $room;
    }
}	