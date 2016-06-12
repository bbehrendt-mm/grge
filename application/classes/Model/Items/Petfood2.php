<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Petfood2 extends Model_Items_Abstract_Item implements Interface_Static {

	protected static $static_info = Array(
			'name' => 'Power-Tierfutter',
			'icon' => 'petfood2',
			'description' => 'Dieses Tierfutter leuchtet im Dunklen... das ist sicher ein gutes Zeichen! Aber, nur um sicher zu gehen, solltest du es lieber an deine Haustiere verfüttern...',
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
                    ->effect(Model_Status::MS_STAT_ENERGY, 5)
                    ->effect(Model_Status::MS_STAT_HEALTH, 5)
                    ->consume($this)
                    ->message('Naja, so richtig gut hat das jetzt nicht geschmeckt... aber wer wird in der Postapokalypse schon wählerisch sein, nicht wahr?')
            )
        )
        ->add_action('Fressen', Model_Action::factory()
            ->allow_for(Interface_Plentity::IC_NPC_ANIMAL)
            ->effect(
                Model_Effect::factory()
                    ->effect(Model_Status::MS_STAT_HUNGER, 20)
                    ->effect(Model_Status::MS_STAT_ENERGY, 20)
                    ->consume($this)
            )
        );
    }
}	