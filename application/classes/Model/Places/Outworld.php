<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Places_Outworld extends Model_Places_Abstract_Node {
	
	protected static $name = 'Die Umgebung des Verstecks';
	protected static $description = 'Früher blühte hier das Leben, jetzt findet man hier nur noch Sand und gelegentlich ein paar Zombies, die in kleinen Grüppchen die Ruinen der Zivilisation umstreifen. Unwahrscheinlich, dass du hier etwas nützliches findest. Eventuell findest du aber das ein oder andere Gebäude, das du nach nützlichen Dingen durchsuchen kannst.';

	private $initial_supply = false;
	
	protected $survival_find = true;
    protected $tickets = Array();
	
	private function initial_supply() {
        /** @global Model_Game $game */
        global $game;
        $this->initial_supply = true;

        if ($game->config('places.outworld.spawn_stranger')) {
            $this->inventory->add(new Model_Items_Body('Leiche eines Schnitzeljägers', 'Sieht so aus als hätte dieser arme Tropf an einer Schnitzeljagt teilgenommen... seine linke Hand hält ein paar unleserliche Schriftstücke fest umklammert, seine rechte einen Text, der mit "Dayan" unterschrieben ist...'));
            $this->inventory->add(new Model_Items_Paracetoid);
            $this->inventory->add(new Model_Items_Paracetin);
            $this->inventory->add(new Model_Items_Machete);
            $this->inventory->add(new Model_Items_Ammobelt);
            $this->inventory->add(new Model_Items_Batgun);
            $this->inventory->add(new Model_Items_Tentkit);

            //Water bottle
            $bottle = new Model_Items_Bottle;
            $bottle->add_water(3, 25);
            $this->inventory->add($bottle);

            $this->log->add(new Model_Log_Types_Text('Verschiedene Gegenstände gefunden', 'Ein hilfreicher Fund', 'Nach nur ein paar Metern findest du ein notdürftig aufgeschlagenes Lager - der Besitzer ist wohl im Schlaf überrascht worden. Naja, wenigstens wird er dann wohl nichts mehr dagegen haben wenn du dich an seiner Ausrüstung bedienst ...'));
        }

        if ($game->config('places.outworld.alt_spawn_stranger')) {
            $this->inventory->add(new Model_Items_Body('Leiche eines Reporters', 'Er hat wohl gehofft, mit der Story über die Zombie-Apokalypse den Pulizer-Preis zu gewinnen. Hoffen wir mal für ihn, dass der auch posthum verliehen wird...'));
            $this->inventory->add(new Model_Items_Paracetoid);
            $this->inventory->add(new Model_Items_Paracetin);
            $this->inventory->add(new Model_Items_Lunchbox());
            $this->inventory->add(new Model_Items_Sportsdrink());
            $this->inventory->add(new Model_Items_Tentkit2);

            $this->log->add(new Model_Log_Types_Text('Verschiedene Gegenstände gefunden', 'Ein hilfreicher Fund', 'Nach nur ein paar Metern findest du ein notdürftig aufgeschlagenes Lager - der Besitzer ist wohl im Schlaf überrascht worden. Naja, wenigstens wird er dann wohl nichts mehr dagegen haben wenn du dich an seiner Ausrüstung bedienst ...'));
        }
	}

    public function pretick() {
        /**
         * @global $game Model_Game
         */
        global $game;

        //Check for zombie attack
        if ($this->initial_supply || !$game->config('places.outworld.spawn_stranger'))
            parent::pretick();
    }

	public function tick($type = Interface_Tickable::IT_TYPE_PLAYER) {
        /**
         * @global $game Model_Game
         * @global $player Interface_Plentity
         */
        global $game, $player;
					
		if ($player->can(Interface_Plentity::IC_TRIGGER_SUPPLIES) && !$this->initial_supply && ($game->config('places.outworld.spawn_stranger') || $game->config('places.outworld.alt_spawn_stranger')))
		{
			$this->initial_supply();
			return true;	
		}

		return parent::tick($type);
	}
}	