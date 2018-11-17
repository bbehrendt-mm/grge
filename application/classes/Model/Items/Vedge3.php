<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Vedge3 extends Model_Items_Abstract_Item implements Interface_Static {

	protected static $static_info = Array(
			'name' => 'Schrumplige Mutationsmelone',
			'icon' => 'vedge3',
			'description' => 'Also einen grünen Daumen hast du bei der Zucht dieser Mutationsmelone nicht bewiesen ...',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_FOOD,
	);

	protected static $weight = 2;

    protected function hid(): Model_Hid {
        return parent::hid()
            ->add_action('Essen', Model_Action::factory()
                ->effect(
                    Model_Effect::factory()
                        ->effect(Model_Status::MS_STAT_HUNGER, 10)
                        ->effect(Model_Status::MS_STAT_THIRST, 5)
                        ->consume($this)
                        ->message('Das wenige vorhandene Fruchtfleisch ist sehnig und zäh, außerdem schmeckt es irgendwie komisch. Naja, besser als nichts...')
                )
            );
    }
}	