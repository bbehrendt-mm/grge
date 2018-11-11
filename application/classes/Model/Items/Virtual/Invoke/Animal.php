<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Virtual_Invoke_Animal extends Model_Items_Virtual_Invoke_Abstract {

    public function trigger_spawn(Model_Places_Abstract_Place $location, Interface_Plentity $player) {
        if (!Tool_Gambling::random(0.3/(1+Globals::CurrentGameF()->count('rnd_animals')))) return;
        Globals::CurrentGameF()->count('rnd_animals', 1);

        if (Globals::CurrentGameF()->config('places.general.spawn_random_animals')) {
            
            $npc = Tool_Gambling::roulette([
                ['chance' => 5, 'value' => Model_NPC_Cat::cls()],
                ['chance' => 5, 'value' => Model_NPC_Dog::cls()],
                ['chance' => 5, 'value' => Model_NPC_Mouse::cls()],
            ]);
            
            /** @var Model_NPC_Animal $npc */
            $npc = new $npc();
            $npc->location_class($location->uin());
            Globals::CurrentGameF()->add_npc($npc);
            $location->log()->add(new Model_Log_Types_Movement(Model_Log_Types_Movement::MOVEMENT_TYPE_ENTER, $npc->id(), true));
        }
    }
}	