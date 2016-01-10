<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Steak extends Model_Items_Abstract_Item implements Interface_Static {

	protected static $static_info = Array(
			'name' => 'Leckeres Steak',
			'icon' => 'steak',
			'description' => 'Dieses Steak ist perfekt gegrillt - es ist bis zur Mitte durchgebraten und noch immer schön saftig. Iss es lieber schnell, bevor ein Zombie es dir streitig macht.',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_FOOD,
	);	

	protected static $weight = 3;

    protected function hid() {
        return parent::hid()
            ->add_action('Essen', Model_Action::factory()
                ->effect(
                    Model_Effect::factory()
                        ->effect(Model_Status::MS_STAT_HUNGER, 35)
                        ->consume($this)
                        ->message('Wirklich lecker, wenn man bedenkt, woraus dieses Steak gemacht wurde...')
                )
            );
    }
}	