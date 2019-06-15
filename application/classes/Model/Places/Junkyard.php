<?php defined('SYSPATH') OR die('No direct access allowed.');

//This is a generic building class that has multiple names; on is randomly selected when this class is constructed
class Model_Places_Junkyard extends Model_Places_Abstract_Place {
	
	protected static $location_name = 'Mülldeponie';
	protected static $description = 'Obwohl hier schon seit Jahren kein neuer Müll mehr gelagert wurde kannst du die Mülldeponie noch immer meilenweit riechen. Das allermeiste, was du hier aus den Müllbergen ziehen kannst, ist zu nichts mehr zu gebrauchen. Allerdings kannst du ja immer auf einen Glücksfund hoffen.';

    protected static $icon = 'landfill';

	private $initial_supply = false;

    public function setup_additional_rooms(): void {
        parent::setup_additional_rooms();

        $this->setup_new_room($this->create_new_room(5,['outside']),
                              ['lf_dump'],
                              []
        )->set_default_state();

        $this->setup_new_room($this->create_new_room(50,['outside']),[],[])->set_default_state();
        $this->setup_new_room($this->create_new_room(50,['outside']),[],[])->set_default_state();
        $this->setup_new_room($this->create_new_room(50,['outside']),[],[])->set_default_state();
        $this->setup_new_room($this->create_new_room(50,['outside']),[],[])->set_default_state();
    }

	private function initial_supply(): void
    {
		$this->initial_supply = true;
		$this->inventory->add(new Model_Items_Body('Steve', 'Auf seinem blauen Overall ist ein Namensschild - "Steve". Anscheinend hat Steve früher hier gearbeitet. Und handwerklich geschickt war er auch, denn neben ihm findest du einen Splitterwerfer. Du hast ganz schön Glück, dass du ständig Tote findest die cooles Zeug dabei haben, weist du das eigentlich?'));
		$this->inventory->add(new Model_Items_Splinter);
		$this->inventory->add(new Model_Items_Splintergun);
		
		$this->log->add(new Model_Log_Types_String('Ein hilfreicher Fund', 'Neben einem kleinen Schuppen findest du hinter einer Wand aus Kisten eine Leiche. Der arme Kerl wollte sich wohl vor den Zombies verstecken. Scheint nicht geklappt zu haben ...'));
	}
	
	public function tick($type = Interface_Tickable::IT_TYPE_PLAYER): bool
    {
		if (!$this->initial_supply && Globals::CurrentPlayerF()->can(Interface_Plentity::IC_TRIGGER_SUPPLIES))
		{
			$this->initial_supply();
			return true;	
		}		
			
		return parent::tick($type);
	}

}	