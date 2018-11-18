<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Virtual_Invoke_AnimalForce extends Model_Items_Virtual_Invoke_Abstract {

    protected $cls;

    public function __construct($animal_class) {
        parent::__construct();
        $this->cls = $animal_class;
    }

    public function trigger_spawn(Model_Places_Abstract_Place $location, Interface_Plentity $player): void
    {

        switch ($this->cls) {

            case 000: $npc = Model_NPC_Cat::cls(); break;
            case 001: $npc = Model_NPC_Special_Winchester::cls(); break;

            case 010: $npc = Model_NPC_Dog::cls(); break;
            case 011: $npc = Model_NPC_Special_Dogmeat::cls(); break;
            case 012: $npc = Model_NPC_Special_Doodle::cls(); break;

            case 020: $npc = Model_NPC_Mouse::cls(); break;
            case 021: $npc = Model_NPC_Special_Sherri::cls(); break;

            case 101: $npc = Model_NPC_Event_Rudolph::cls(); break;
            case 102: $npc = Model_NPC_Event_RudolphBR::cls(); break;
            case 103: $npc = Model_NPC_Event_Conductor::cls(); break;

            case 201: $npc = Model_NPC_Event_Crow::cls(); break;
            case 202: $npc = Model_NPC_Event_Merchant::cls(); break;
            case 203: $npc = Model_NPC_Event_Patient::cls(); break;
            case 204: $npc = Model_NPC_Event_Clown::cls(); break;

            default: return;
        }


        /** @var Model_NPC_Animal $npc */
        $npc = new $npc();
        $npc->location_class($location->uin());
        Globals::CurrentGameF()->add_npc($npc);
        $location->log()->add(new Model_Log_Types_Movement(Model_Log_Types_Movement::MOVEMENT_TYPE_ENTER, $npc->id(), true));
    }

}	