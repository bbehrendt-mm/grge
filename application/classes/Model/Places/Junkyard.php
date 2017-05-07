<?php defined('SYSPATH') OR die('No direct access allowed.');

//This is a generic building class that has multiple names; on is randomly selected when this class is constructed
class Model_Places_Junkyard extends Model_Places_Abstract_Place {
	
	protected static $name = 'Mülldeponie';
	protected static $description = 'Obwohl hier schon seit Jahren kein neuer Müll mehr gelagert wurde kannst du die Mülldeponie noch immer meilenweit riechen. Das allermeiste, was du hier aus den Müllbergen ziehen kannst, ist zu nichts mehr zu gebrauchen. Allerdings kannst du ja immer auf einen Glücksfund hoffen.';

    protected static $icon = 'landfill';
	
	private $splinter_load = 0;
	private $initial_supply = false;

    public function uin($new = null) {
        if ($new !== null) {
            $this->inventory->add(new Model_Items_Virtual_Location_Landfill());
        }
        return parent::uin($new);
    }
	
	public function splinters($dif = null) {
		if ($dif === null)
            return $this->splinter_load;
        else return $this->splinter_load += $dif;
	}
	
	private function initial_supply() {
		$this->initial_supply = true;
		$this->inventory->add(new Model_Items_Body('Steve', 'Auf seinem blauen Overall ist ein Namensschild - "Steve". Anscheinend hat Steve früher hier gearbeitet. Und handwerklich geschickt war er auch, denn neben ihm findest du einen Splitterwerfer. Du hast ganz schön Glück, dass du ständig Tote findest die cooles Zeug dabei haben, weist du das eigentlich?'));
		$this->inventory->add(new Model_Items_Splinter);
		$this->inventory->add(new Model_Items_Splintergun);
		
		$this->log->add(new Model_Log_Types_Text('Verschiedene Gegenstände gefunden', 'Ein hilfreicher Fund', 'Neben einem kleinen Schuppen findest du hinter einer Wand aus Kisten eine Leiche. Der arme Kerl wollte sich wohl vor den Zombies verstecken. Scheint nicht geklappt zu haben ...'));
	}
	
	public function tick($type = Interface_Tickable::IT_TYPE_PLAYER) {
		if (!$this->initial_supply && Globals::CurrentPlayer()->can(Interface_Plentity::IC_TRIGGER_SUPPLIES))
		{
			$this->initial_supply();
			return true;	
		}		
			
		return parent::tick($type);
	}

}	