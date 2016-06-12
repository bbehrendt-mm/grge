<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Petfood3 extends Model_Items_Abstract_Item implements Interface_Static {

	protected static $static_info = Array(
			'name' => 'Abgelaufenes Tierfutter',
			'icon' => 'petfood3',
			'description' => 'Ist es eigentlich normal, dass dieses Tierfutter sich von selbst bewegt... und außerdem anfängt, die Dose in der es sich befindet aufzulösen?',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_FOOD,
	);

	protected static $weight = 1;

    protected function hid() {
    return parent::hid()
        ->add_action('Essen', Model_Action::factory()
            ->allow_for(Interface_Plentity::IC_NPC_NONPC)
            ->effect(
                Model_Effect::factory()
                    ->effect(Model_Status::MS_STAT_HUNGER, 5)
                    ->effect(Model_Status::MS_STAT_HEALTH, -15)
                    ->buff(Model_Buffs_Poison::cls())
                    ->consume($this)
                    ->message('Naja, so richtig gut hat das jetzt nicht geschmeckt... aber wer wird in der Postapokalypse schon wählerisch sein, nicht wahr?')
            )
        )
        ->add_action('Fressen', Model_Action::factory()
            ->allow_for(Interface_Plentity::IC_NPC_ANIMAL)
            ->effect(
                Model_Effect::factory()
                    ->effect(Model_Status::MS_STAT_HUNGER, 20)
                    ->effect(Model_Status::MS_STAT_HEALTH, -5)
                    ->buff(Model_Buffs_Poison::cls())
                    ->consume($this)
            )
        );
    }
}	