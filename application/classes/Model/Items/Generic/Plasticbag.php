<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Generic_Plasticbag extends Model_Items_Abstract_Item implements Interface_Fillable, Interface_Static {

	protected static $static_info = Array(
			'name' => 'Plastiktüte',
			'icon' => 'plasticbag',
			'description' => 'Plastiktüten sind praktisch unverrottbar, daher findet man sie auch nach der Zombieapokalypse an jeder Ecke. Leider sind sie weder sonderlich nützlich noch wertvoll. Naja, wenn du an Wasserüberschuss leidest könntest du eine Wasserbombe daraus bauen ...',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_RES,
	);
	
	protected static $weight = 1;

    public function hid() {
        return Model_Hid::factory()
            ->add_action('Füllen oder Leeren...',
                Model_Action::factory()
                    ->javascript(
                        Model_Javascript::factory()
                            ->close_qtip()
                            ->versa('water')
                    )
            );
    }
	
	private function produce_waterbomb() {
        /**
         * @global $player Model_Player
         */
        global $player;
		$this->consume();
		
		if ($player->job(1040, 4, false))
			$player->location()->inventory()->add(new Model_Items_Hwaterbomb);
		else $player->location()->inventory()->add(new Model_Items_Waterbomb);		
	}
	
	public function interaction_fillfrom($item) {
        /**
         * @global $player Model_Player
         */
        global $player;
		
		if (Tool_System::instance_of($item, 'Model_Items_Abstract_Bottle'))
		{
			/** @var $item Model_Items_Abstract_Bottle */
            if ($item->get_water(1)) $this->produce_waterbomb();
			else $player->log()->add(new Model_Log_Types_Text(null, null, 'Leider ist in dieser Flasche nicht mehr genug Wasser, um die Plastiktüte zu füllen ...'));
		}
		else return false;
        return false;
	}
	
	public function interaction_fill($item) {
		if (Tool_System::instance_of($item, 'Model_Items_Abstract_Liquid')) {
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