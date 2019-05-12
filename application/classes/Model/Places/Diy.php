<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Places_Diy extends Model_Places_Abstract_Place {
	
	protected static $namelist = Array('Baumarkt "Meister Hämmerlein"', 'Baumarkt "EKEA"', 'Baumarkt "D-I-Y"');
	protected static $description = 'Sobald du in deinem Versteck eine Werkbank errichtet hast, solltest du so oft wie möglich im Baumarkt vorbei schauen. Hier gibt\'s praktisch alles was das Bastlerherz begehrt. Leider gibt es hier auch einige Zombies, du solltest also besser auf alles vorbereitet sein ...';
    protected static $icon = 'diy';
    protected static $outside = false;

    public function setup_additional_rooms(): void
    {
        parent::setup_additional_rooms();
        $this->create_new_room(50,['inside'])->set_default_state();
    }

    protected static $user_rooms_allowed = 1;
    protected static $user_rooms_size = 15;
    public function setup_user_room(): ?Model_Room
    {
        $room = parent::setup_user_room();
        if ($room !== null) {
            $room->name( "Pausenraum", true  );
            $room->add_tag(["inside"]);
            $room->set_default_state();

            Model_Blueprints::fast_apply($this, 'rooms', "kitchen", $room);
            Model_Blueprints::fast_apply($this, 'upgrades', ["ktc2"],  $room);
        }
        return $room;
    }
}	