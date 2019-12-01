<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Places_Druglab extends Model_Places_Abstract_Place {
	
	protected static $location_name = 'Drogenlabor';
	protected static $description = 'In diesem heruntergekommenen Schuppen wurden jahrelang diverse Mittelchen mit eher kontroverser Wirkung produziert. Es ist immer noch einiges an Equipment da, das du sicher für irgendwas nutzen kannst. Leider haben auch die Zombies Gefallen an diesem Örtchen gefunden... allerdings weniger wegen dem Equipment, sondern eher wegen den wehrlosen Junkies, die sich hier herumtreiben.';
    protected static $icon = 'lab';
    protected static $outside = false;

    public function setup_additional_rooms(): void {
        parent::setup_additional_rooms();
        $this->setup_new_room($this->create_new_room( 20,['inside']),
                              ['kitchen', 'kitchen_meth'],
                              [],
                              'Drogenküche'
        )->set_default_state();
        $this->create_new_room(15,['inside'])->set_default_state();
    }

	public function uin($uin = NULL) {
		if ($uin === NULL) return parent::uin();
		else $t = parent::uin($uin);
		
		$count = random_int(3,15);
		for ($i = 0; $i < $count; $i++) $this->inventory->add(new Model_Items_Drugpack());
		
		$this->inventory->add(new Model_Items_Vending(get_class($this),
            'Drogotron'
        ));
        return $t;
	}

    protected static $user_rooms_allowed = 1;
    protected static $user_rooms_size = 10;
    public function setup_user_room(): ?Model_Room
    {
        $room = parent::setup_user_room();
        if ($room !== null) {
            $room->name( "Geheimer Raum", true  );
            $room->add_tag(["inside"]);
            $room->set_default_state();

            Model_Blueprints::fast_apply($this, 'rooms', ['kitchen', 'kitchen_slaughter'], $room);
        }
        return $room;
    }
}	