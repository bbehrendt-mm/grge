<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Places_Mall extends Model_Places_Abstract_Place {
	
	protected static $location_name = 'Gigantisches Einkaufszentrum';
	protected static $description = 'Früher strömten Menschen von Nah und Fern an diesen Ort, um sich dem zügellosen Konsumrausch hinzugeben. Und noch immer ist dieses Einkaufszentrum gut besucht - nur leider von Zombies, die ziellos durch die Gänge streunen. Wenn du den Mut hast dich ihnen zu stellen wirst du hier sicher viele Gegenstände finden.';
    protected static $outside = false;

    protected static $icon = 'mall';

	public function uin($uin = NULL) {
		if ($uin === NULL) return parent::uin();
		else $t = parent::uin($uin);
	
		$this->inventory->add(new Model_Items_Vending(get_class($this),
            'Jumbomax Mallmaster'
        ));
        $this->inventory->add(new Model_Items_Vending2());
        return $t;
	}

    protected static $user_rooms_allowed = 1;
    protected static $user_rooms_size = 25;
    public function setup_user_room(): ?Model_Room
    {
        $room = parent::setup_user_room();
        if ($room !== null) {
            $room->name( "Pausenraum", true  );
            $room->add_tag(["inside"]);
            $room->set_default_state();

            Model_Blueprints::fast_apply($this, 'rooms', "kitchen", $room);
            Model_Blueprints::fast_apply($this, 'upgrades', ["ktc2","ktc3","ktc4"],  $room);
        }
        return $room;
    }
	
}	