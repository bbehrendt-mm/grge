<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Coffee2 extends Model_Items_Abstract_Item implements Interface_Static {

	protected static $static_info = Array(
			'name' => 'Heißer Kaffee',
			'icon' => 'cof_hot',
			'description' => 'Heißer Kaffee... welch ein Luxus. Dank deines Wasserkochers ist dieser Kaffee übrigens so heiß, dass er niemals wieder kalt wird. Für die einen wäre das einfach eine Lücke in der Spiellogik, für andere aber ..... heißer Kaffee!',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_FOOD,
	);

	protected static $weight = 2;

    protected function hid() {
        return parent::hid()
            ->add_action('Trinken', Model_Action::factory()
                ->effect(
                    Model_Effect::factory()
                        ->effect(Model_Status::MS_STAT_SLEEPY, 50 * ((Tool_Scripts::get_timeofday() == "morning") ? 1.5 : 1))
                        ->effect(Model_Status::MS_STAT_ENERGY, 15 * ((Tool_Scripts::get_timeofday() == "morning") ? 1.5 : 1))
                        ->consume($this)
                        ->message('Aaah, das tut gut. Deine Müdigkeit verschwindet und du bekommst neue Energie.')
                )
            );
    }
}	