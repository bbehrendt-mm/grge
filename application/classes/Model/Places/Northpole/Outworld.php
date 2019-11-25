<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Places_Northpole_Outworld extends Model_Places_Outworld {
	
	protected static $location_name = 'Die Umgebung des Verstecks';
	protected static $description = 'Früher blühte hier das Leben, jetzt findet man hier nur noch Sand und gelegentlich ein paar Zombies, die in kleinen Grüppchen die Ruinen der Zivilisation umstreifen. Unwahrscheinlich, dass du hier etwas nützliches findest. Eventuell findest du aber das ein oder andere Gebäude, das du nach nützlichen Dingen durchsuchen kannst.';

	protected function spawn_companion() {
        if (!Globals::CurrentGameF()->get_npc('rudolph_br')) {
            $rudolph = new Model_NPC_Event_RudolphBR();
            $rudolph->location_class($this->uin());
            Globals::CurrentGameF()->add_npc($rudolph, 'rudolph_br');
            $this->log()->add(new Model_Log_Types_Movement(Model_Log_Types_Movement::MOVEMENT_TYPE_ENTER, $rudolph->id(), true));
        }
    }

    protected static $temperature_engine = -10;
}	