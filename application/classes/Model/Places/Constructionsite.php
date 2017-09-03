<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Places_Constructionsite extends Model_Places_Abstract_Place {
	
	protected static $name = 'Baustelle';
	protected static $description = 'Was hier mal gebaut werden sollte ist nicht erkennbar - man sieht nur verrostete Eisenträger und Baumaterial, das am Boden liegt. Einige Zombies haben die Baustelle offenbar zu ihrem Wohnsitz erkoren. Glücklicherweise haben sie hier praktisch keine Möglichkeit, einen Überraschungsangriff zu starten.';
    protected static $icon = 'site';

    public function setup_additional_rooms() {
        parent::setup_additional_rooms();

        $this->create_new_room(40,['outside']);
        $this->create_new_room(40,['outside']);

        for ($i = 0; $i < 3; $i++)
            $this->setup_new_room($this->create_new_room(6,['inside']),
                                  ['container_closed'],
                                  [],
                                  "Container");
    }
}	