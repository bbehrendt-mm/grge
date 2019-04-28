<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Places_Villa extends Model_Places_Abstract_Place {
	
	protected static $location_name = 'Dein altes Herrenhaus';
	protected static $description = 'Früher hast du hier einmal wie ein König gelebt. Du hattest Bedienstete, erstklassige Einrichtung sowie einen Pudel namens Coco. Dann kamen die Zombies, und du musstest fliehen.... vom einstigen Glanz dieses Anwesens ist kaum noch etwas übrig geblieben, aber zumindest kannst du deine Sachen nach etwas durchsuchen, was dir im Kampf gegen die Zombies hilft.';
    protected static $icon = 'mansion';
    protected static $outside = false;

    public function setup_additional_rooms(): void
    {
        parent::setup_additional_rooms();
        $this->create_new_room(10,['inside'])->set_default_state();
        $this->create_new_room(10,['inside'])->set_default_state();
        $this->create_new_room(10,['inside'])->set_default_state();
        $this->create_new_room(15,['inside'])->set_default_state();
        $this->create_new_room(20,['inside'])->set_default_state();
        $this->create_new_room(20,['inside'])->set_default_state();
    }
}	