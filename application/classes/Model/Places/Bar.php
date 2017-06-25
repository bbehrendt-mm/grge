<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Places_Bar extends Model_Places_Abstract_Place {
	
	protected static $namelist = Array('Schäbige Bar', 'Dunkle Kaschemme', 'Verfallene Kneipe');
	protected static $description = 'Eigentlich hat sich an diesem Ort mit der Zombieapokalypse nicht allzu viel geändert... an der Bar wird billiges, lauwarmes Bier getrunken und überall tummeln sich torkelnde, übelriechende Gestalten. Eigentlich ist der einzige Unterschied zu früher, dass einem nicht mehr nur die Brieftasche, sondern auch diverse innere Organe bei einem Besuch hier abhanden kommen könnten.';
    protected static $icon = 'bar';
    protected static $outside = false;

    public function tick($type = Interface_Tickable::IT_TYPE_PLAYER) {
        if (Globals::CurrentPlayer()->can(Interface_Plentity::IC_TRIGGER_SUPPLIES)) {

            if (Globals::CurrentGame()->config('places.bar.spawn_winchester') && !Globals::CurrentGame()->get_npc('winchester')) {
                $winchester = new Model_NPC_Special_Winchester();
                $winchester->location_class($this->uin());
                Globals::CurrentGame()->add_npc($winchester, 'winchester');
                $this->log()->add(new Model_Log_Types_Movement(Model_Log_Types_Movement::MOVEMENT_TYPE_ENTER, $winchester->id(), true));
            }
        }

        return parent::tick($type);
    }

    public function setup_additional_rooms() {
        parent::setup_additional_rooms();
        $this->create_new_room( 5,false);
        $this->create_new_room(10,false);
    }
}	