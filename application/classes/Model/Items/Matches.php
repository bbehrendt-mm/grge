<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Matches extends Model_Items_Abstract_Stackable {
	
	protected static $weight = 1;
	protected static $max_size = 6;
	protected static $autoappender = Array('Streichholz', 'Streichhölzer');

    protected static $autospawn = Array(1,2);

    protected static $static_info = Array(
        'name' => 'Streichholzschachtel',
        'icon' => 'matches',
        'description' => 'Eine ordinäre Streichholzschachtel... es sind sogar noch in paar Streichhölzer darin!',
        'category' => Model_Items_Abstract_Item::MIAI_CAT_GEAR,
    );

    protected static $pill_effects = Array();

    protected function hid(): Model_Hid {
        return parent::hid()
            ->add_action('Kleines Feuer machen', Model_Action::factory()
                ->requirement(Model_Items_Generic_Crwood::cls(), 1)
                ->effect(
                    Model_Effect::factory()
                        ->consume($this)
                        ->spawn(Model_Items_Generic_Fire::cls())
                        ->message('Du hast ein kleines Feuer entfacht.')
                )
            )
            ->add_action('Großes Feuer machen', Model_Action::factory()
                ->requirement(Model_Items_Generic_Wood::cls(), 1)
                ->effect(
                    Model_Effect::factory()
                        ->consume($this)
                        ->spawn(Model_Items_Generic_Fire2::cls())
                        ->message('Du hast ein großes Feuer entfacht.')
                )
            );
    }
}	