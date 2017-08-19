<?php defined('SYSPATH') OR die('No direct access allowed.');

//This is a generic building class that has multiple names; on is randomly selected when this class is constructed
class Model_Places_Burgerjoint extends Model_Places_Abstract_Place {
	
	protected static $namelist = Array('"MacUndead" Restaurant', '"ZombieKing" Restaurant', '"Kentucky Fried Eyeballs" Restaurant', '"Ostsee" Restaurant', '"Corpseland" Restaurant', '"Bloodway" Restaurant');
	protected static $description = 'Dieses Franchise hat schonmal bessere Zeiten erlebt ... Die dicken Kinder, die sich hier früher Essensschlachten geliefert haben sind schon lange verschwunden - dafür schleichen nun Zombies zwischen den Tischen und in der Küche umher. Die gute Nachricht ist: Selbst ein Zombie würde das Zeug, das hier serviert wurde, nicht anrühren - du kannst hier also bestimmt noch ein paar Combomenüs abstauben.';
    protected static $icon = 'restaurant';
    protected static $outside = false;

    public function setup_additional_rooms() {
        parent::setup_additional_rooms();
        $r_kitchen = $this->create_new_room(10,['inside']);

        Model_Blueprints::fast_apply($this,'rooms',['kitchen','kitchen_burgerjoint'], $r_kitchen);
        $r_kitchen->name("Küchenbereich");

        $r_cooler = $this->create_new_room(10,['inside']);
        Model_Blueprints::fast_apply($this,'rooms','cooler_closed', $r_cooler);
        $r_cooler->name("Kühlkammer");

        $this->create_new_room(20,['inside']);
    }
}	