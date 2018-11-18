<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Places_Diy extends Model_Places_Abstract_Place {
	
	protected static $namelist = Array('Baumarkt "Meister Hämmerlein"', 'Baumarkt "EKEA"', 'Baumarkt "D-I-Y"');
	protected static $description = 'Sobald du in deinem Versteck eine Werkbank errichtet hast, solltest du so oft wie möglich im Baumarkt vorbei schauen. Hier gibt\'s praktisch alles was das Bastlerherz begehrt. Leider gibt es hier auch einige Zombies, du solltest also besser auf alles vorbereitet sein ...';
    protected static $icon = 'diy';
    protected static $outside = false;

    public function setup_additional_rooms(): void
    {
        parent::setup_additional_rooms();
        $this->create_new_room(50,['inside']);
    }
}	