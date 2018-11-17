<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Cookie2 extends Model_Items_Abstract_Item implements Interface_Static {
	
	protected static $static_info = Array(
			'name' => 'Besonderes Weihnachtsplätzchen',
			'icon' => 'cookie2',
			'description' => 'Allein der Duft dieses leckeren Plätzchens lässt dich alles um dich herum vergessen - hauptsächlich wegen der in den Teig gemischten Drogen. Dieses Plätzchen verbreitet zwar nicht unbedingt Weihnachtsstimmung, aber du siehst nach seinem Genuss zumindest Sterne!',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_FOOD,
	);

	protected static $weight = 0.2;

    protected function hid(): Model_Hid {
        return parent::hid()
            ->add_action('Essen', Model_Action::factory()
                ->effect(
                    Model_Effect::factory()
                        ->effect(Model_Status::MS_STAT_HUNGER, 4)
                        ->effect(Model_Status::MS_STAT_HEALTH, -8)
                        ->effect(Model_Status::MS_STAT_DRUNK, 10)
                        ->effect(Model_Status::MS_STAT_ENERGY, 8)
                        ->consume($this)
                        ->message('Du schlingst das Plätzchen herunter. Es dauert nur wenige Sekunden bis es seine Wirkung entfaltet - jetzt pass nur auf dass du dich nicht im Glauben, ein fliegendes Rentier zu sein, vom Dach stürzt...')
                )
            );
    }
}	