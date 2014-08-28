<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Abstract_Liquid extends Model_Items_Abstract_Item implements Interface_Tmpitem {
	
	protected $toxicity;
	protected static $weight = 0;

    public function hid() {
        return Model_Hid::factory()
            ->add_action('Umfüllen ...',
                Model_Action::factory()
                    ->javascript(
                        Model_Javascript::factory()
                            ->close_qtip()
                            ->versa('water')
                    )
            );
    }

	public function __construct($toxicity = 0) {
		$this->toxicity = $toxicity;
	}
	
	public function toxicity() {
		return $this->toxicity;
	}
	
	public function take($silent = false) {
		global $game, $player;
		
		if (!$silent) $player->log()->add(new Model_Log_Types_Text(null, null, 'Du benötigst ein Gefäß, um diese Flüssigkeit transportieren zu können.'));
		return false;
	}
}	