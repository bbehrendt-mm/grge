<?php defined('SYSPATH') OR die('No direct access allowed.');

abstract class Model_Items_Abstract_Liquid extends Model_Items_Abstract_Item implements Interface_Tmpitem {
	
	protected $toxicity;
	protected static $weight = 0;

	public function __construct($toxicity = 0) {
		$this->toxicity = $toxicity;
		parent::__construct();
	}
	
	public function toxicity() {
		return $this->toxicity;
	}
	
	public function take($silent = false): bool
    {
		return false;
	}

    protected function hid(): Model_Hid {
        return parent::hid()
            ->add_action('Auflecken',
                Model_Action::factory()
                    ->allow_for(Interface_Plentity::IC_NPC_ANIMAL)
                    ->show_as(
                        Model_Effect::factory()
                            ->effect(Model_Status::MS_STAT_THIRST, 20)
                            ->ambiguous_effect(Model_Status::MS_STAT_HEALTH)
                    )
                    ->effect(
                        Model_Effect::factory()
                            ->effect(Model_Status::MS_STAT_THIRST, 20)
                            ->effect(Model_Status::MS_STAT_HEALTH, -$this->toxicity)
                            ->consume($this)
                    )
            );
    }
}	