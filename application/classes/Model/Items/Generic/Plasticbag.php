<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Generic_Plasticbag extends Model_Items_Abstract_Item implements Interface_Fillable, Interface_Static {

	protected static $static_info = Array(
			'name' => 'Plastiktüte',
			'icon' => 'plasticbag',
			'description' => 'Plastiktüten sind praktisch unverrottbar, daher findet man sie auch nach der Zombieapokalypse an jeder Ecke. Leider sind sie weder sonderlich nützlich noch wertvoll. Naja, wenn du an Wasserüberschuss leidest könntest du eine Wasserbombe daraus bauen ...',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_RES,
	);
	
	protected static $weight = 1;
	
	private function produce_waterbomb() {
		$this->consume();
		
		if (!Globals::shadowPlayerExists() &&
            (Globals::PrimaryPlayerF()->job(1040, 4, false) || Globals::PrimaryPlayerF()->job(1041)))

		    Globals::CurrentPlayerF()->location()->inventory()->add(new Model_Items_Hwaterbomb);
		else Globals::CurrentPlayerF()->location()->inventory()->add(new Model_Items_Waterbomb);
	}
	
	public function interaction_fillfrom($item) {
		if (Tool_System::instance_of($item, Model_Items_Abstract_Bottle::cls()))
		{
			/** @var $item Model_Items_Abstract_Bottle */
            if ($item->get_water(1)) $this->produce_waterbomb();
			else Globals::PrimaryPlayerF()->log()->add(new Model_Log_Types_String( null, 'Leider ist in dieser Flasche nicht mehr genug Wasser, um die Plastiktüte zu füllen ...'));
		}
		else return false;
        return false;
	}
	
	public function interaction_fill($item) {
		if (Tool_System::instance_of($item, Model_Items_Abstract_Liquid::cls())) {
			/** @var $item Model_Items_Abstract_Liquid */
            $item->consume();
			$this->produce_waterbomb();
            return true;
		}
		else return false;		
	}
	
	public function capacity() {
		return 1;
	}
	
	public function fillrate() {
		return 0;
	}
}	