<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Places_Northpole_Outworld extends Model_Places_Outworld {
	
	protected static $location_name = 'Die Eiswüste';
    protected static $icon = 'np_desert';
	protected static $description = 'Dieser Ort bietet große optische Abwechslung - zwischen Schneebergen, Schneebergen und noch mehr Schneebergen ragen Schneeberge in die Höhe.';

    protected function spawn_stranger_normal() {
        $this->inventory->add(new Model_Items_Body('Leiche eines Elfen', 'Sieht so aus, als wäre der arme Tropf von Geschenken erschlagen worden, die aus Santas Schlitten gefallen sind...'));
        $this->inventory->add(new Model_Items_Paracetoid());
        $this->inventory->add(new Model_Items_Paracetin());
        $this->inventory->add(new Model_Items_Machete());
        $this->inventory->add(new Model_Items_Ammobelt());
        $this->inventory->add(new Model_Items_Batgun());
        $this->inventory->add(new Model_Items_Matches(4));

        //Water bottle
        $bottle = new Model_Items_Bottle();
        $bottle->add_water(4, 0);
        $this->inventory->add($bottle);

        $this->inventory->add(new Model_Items_Lunchbag());
        $this->inventory->add(new Model_Items_Whiskey());
        $this->inventory->add(new Model_Items_Whiskey());
    }

	protected function spawn_companion() {
        if (!Globals::CurrentGameF()->get_npc('rudolph_br')) {
            $rudolph = new Model_NPC_Event_RudolphBR();
            $rudolph->location_class($this->uin());
            Globals::CurrentGameF()->add_npc($rudolph, 'rudolph_br');
            $this->log()->add(new Model_Log_Types_Movement(Model_Log_Types_Movement::MOVEMENT_TYPE_ENTER, $rudolph->id(), true));
        }
    }

    protected static $temperature_engine = 0;
}	