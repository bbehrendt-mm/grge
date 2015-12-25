<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Coffee extends Model_Items_Abstract_Item implements Interface_Static {

	protected static $static_info = Array(
			'name' => 'Kalter Kaffee',
			'icon' => 'cof_cold',
			'description' => 'Nichts geht über einen kalten Kaffee, um den Tag zu beginnen.... naja, vielleicht ein heißer Kaffee. Nichtsdestotrotz versorgt dich dieses erkaltete Heißgetränk mit frischer Energie und bekämpft deine Müdigkeit.',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_FOOD,
	);
	
	protected static $weight = 2;

    protected function hid() {
        return parent::hid()
            ->add_action('Trinken', Model_Action::factory()
                    ->effect(
                        Model_Effect::factory()
                            ->effect(Model_Status::MS_STAT_SLEEPY, 20 * ((Tool_Scripts::get_timeofday() == "morning") ? 1.5 : 1))
                            ->effect(Model_Status::MS_STAT_ENERGY, 10 * ((Tool_Scripts::get_timeofday() == "morning") ? 1.5 : 1))
                            ->consume($this)
                            ->message('Aaah, das tut gut. Deine Müdigkeit verschwindet und du bekommst neue Energie.')
                    )
            );
    }
}	