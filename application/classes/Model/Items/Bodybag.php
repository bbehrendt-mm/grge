<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Bodybag extends Model_Items_Abstract_Item implements Interface_Static {

	protected static $static_info = Array(
			'name' => 'Leichensack',
			'icon' => 'bodybag',
			'description' => 'Mit diesem Leichensack kannst du - Überraschung - Leichen transportieren. Schön verpackt ist so ein Körper viel einfacher zu transportieren, wenn du also auf Leichenjagd gehst solltest du so einen Sack immer dabei haben.',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_GEAR,
	);
	
	protected static $weight = 3;

    protected function hid() {
        return parent::hid()
            ->add_action('Leiche einsacken', Model_Action::factory()
                    ->requirement('Model_Items_Body', 1)
                    ->requirement(Model_Status::MS_STAT_ENERGY, 5)
                    ->effect(
                        Model_Effect::factory()
                            ->consume($this)
                            ->spawn('Model_Items_Bodybag3')
                            ->message('Du musst ein bisschen drücken, quetschen und pressen, aber irgendwann steckt diese Leiche komplett in deinem Leichensack. Jetzt kannst du sie viel einfacher transportieren, hurra!')
                    )
            );
    }
}	