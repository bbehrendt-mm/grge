<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Places_Northpole_House_Secondfloor extends Model_Places_House_Secondfloor {
	
	protected static $location_name = 'Erste Etage des verbrannten Hauses';
	protected static $description = 'Hier oben sind die Brandschäden weitaus schlimmer als im Erdgeschoss... wahrscheinlich ist das Feuer hier oben ausgebrochen. Anscheinend bist du der Erste, der sich hier hoch getraut hat - keine Spuren von Zombies oder anderen Plünderern.';
    protected static $outside = false;

	protected static $weight_limit = 60;

	public function pretick(): void {
        parent::pretick();

        $ff = Globals::CurrentGameF()->map($this->uin())->get_by_fixed_id(1);
        if (!$ff) return;
        foreach (Tool_Scripts::at_location($this->uin(), false, true) as $npc)
            /** @var Model_NPC_Event_Rudolph $npc */
            if (Tool_System::instance_of($npc, Model_NPC_Event_Rudolph::cls())) {

                if ($npc->get_status()->retrieve('fragile/tumble') && $this->can_leave($npc->id(), true, Interface_Tickable::IT_TYPE_NPC) && Tool_Gambling::random(0.5)) {

                    $this->leave($npc->id(), Interface_Tickable::IT_TYPE_NPC);

                    $npc->location_class($ff->uin());
                    $ff->enter($npc->id(), Interface_Tickable::IT_TYPE_NPC);

                    foreach (Tool_Scripts::at_location($this->uin(), true, false) as $player)
                        $player->log()->add( ':name ist soeben durch den Boden gebrochen und ein Stockwerk tiefer gelandet. Vielleicht solltest du hier nicht mit einem sturzbetrunkenen Rentier durch die Gegend laufen?', [':name' => $npc->name()] );
                }

            }
    }
}	