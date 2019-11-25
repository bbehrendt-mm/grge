<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Places_Outworld extends Model_Places_Abstract_Node {
	
	protected static $location_name = 'Die Umgebung des Verstecks';
	protected static $description = 'Früher blühte hier das Leben, jetzt findet man hier nur noch Sand und gelegentlich ein paar Zombies, die in kleinen Grüppchen die Ruinen der Zivilisation umstreifen. Unwahrscheinlich, dass du hier etwas nützliches findest. Eventuell findest du aber das ein oder andere Gebäude, das du nach nützlichen Dingen durchsuchen kannst.';

	private $initial_supply = false;

	protected static $survival_find_available = false;
    protected $tickets = Array();

    protected function spawn_companion() {
        if (!Globals::CurrentGameF()->get_npc('dogmeat')) {
            $dogmeat = new Model_NPC_Special_Dogmeat();
            $dogmeat->location_class($this->uin());
            Globals::CurrentGameF()->add_npc($dogmeat, 'dogmeat');
            $this->log()->add(new Model_Log_Types_Movement(Model_Log_Types_Movement::MOVEMENT_TYPE_ENTER, $dogmeat->id(), true));
        }
    }

    protected function spawn_stranger_normal() {
        $this->inventory->add(new Model_Items_Body('Leiche eines Schnitzeljägers', 'Sieht so aus als hätte dieser arme Tropf an einer Schnitzeljagt teilgenommen... seine linke Hand hält ein paar unleserliche Schriftstücke fest umklammert, seine rechte einen Text, der mit "Dayan" unterschrieben ist...'));
        $this->inventory->add(new Model_Items_Paracetoid());
        $this->inventory->add(new Model_Items_Paracetin());
        $this->inventory->add(new Model_Items_Machete());
        $this->inventory->add(new Model_Items_Ammobelt());
        $this->inventory->add(new Model_Items_Batgun());
        $this->inventory->add(new Model_Items_Tentkit());

        //Water bottle
        $bottle = new Model_Items_Bottle();
        $bottle->add_water(3, 25);
        $this->inventory->add($bottle);

        if (Globals::CurrentGameF()->config('places.outworld.spawn_stranger_ext')) {
            $this->inventory->add(new Model_Items_Lunchbag());
            $this->inventory->add(new Model_Items_Lunchbag());
            $this->inventory->add(new Model_Items_Bandage2());
        }
    }

    protected function spawn_stranger_alt() {
        $this->inventory->add(new Model_Items_Body('Leiche eines Reporters', 'Er hat wohl gehofft, mit der Story über die Zombie-Apokalypse den Pulizer-Preis zu gewinnen. Hoffen wir mal für ihn, dass der auch posthum verliehen wird...'));
        $this->inventory->add(new Model_Items_Paracetoid());
        $this->inventory->add(new Model_Items_Paracetin());
        $this->inventory->add(new Model_Items_Lunchbox());
        $this->inventory->add(new Model_Items_Sportsdrink());
        $this->inventory->add(new Model_Items_Tentkit2());
    }

	private function initial_supply(): void
    {
        $this->initial_supply = true;
        if (Globals::CurrentGameF()->config('places.outworld.spawn_stranger')) {

            $this->spawn_stranger_normal();
            $this->log->add(new Model_Log_Types_String('Ein hilfreicher Fund', 'Nach nur ein paar Metern findest du ein notdürftig aufgeschlagenes Lager - der Besitzer ist wohl im Schlaf überrascht worden. Naja, wenigstens wird er dann wohl nichts mehr dagegen haben wenn du dich an seiner Ausrüstung bedienst ...'));
        }

        if (Globals::CurrentGameF()->config('places.outworld.alt_spawn_stranger')) {

            $this->spawn_stranger_alt();
            $this->log->add(new Model_Log_Types_String('Ein hilfreicher Fund', 'Nach nur ein paar Metern findest du ein notdürftig aufgeschlagenes Lager - der Besitzer ist wohl im Schlaf überrascht worden. Naja, wenigstens wird er dann wohl nichts mehr dagegen haben wenn du dich an seiner Ausrüstung bedienst ...'));
        }
	}

    public function pretick(): void
    {
        //Check for zombie attack
        if ($this->initial_supply || (!Globals::CurrentGameF()->config('places.outworld.spawn_stranger') && !Globals::CurrentGameF()->config('places.outworld.alt_spawn_stranger')))
            parent::pretick();
    }

	public function tick($type = Interface_Tickable::IT_TYPE_PLAYER): bool
    {
        if (Globals::CurrentPlayerF()->can(Interface_Plentity::IC_TRIGGER_SUPPLIES)) {
            if (!$this->initial_supply && (Globals::CurrentGameF()->config('places.outworld.spawn_stranger') || Globals::CurrentGameF()->config('places.outworld.alt_spawn_stranger')))
            {
                $this->initial_supply();
                return true;
            }

            if (Globals::CurrentGameF()->config('places.outworld.spawn_dogmeat')) $this->spawn_companion();
        }

        return parent::tick($type);
	}
}	