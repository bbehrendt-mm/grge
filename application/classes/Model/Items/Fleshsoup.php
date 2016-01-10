<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Fleshsoup extends Model_Items_Abstract_Item implements Interface_Static {

	protected static $static_info = Array(
			'name' => 'Tomatencremesuppe (?)',
			'icon' => 'fleshsoup',
			'description' => 'Diese ... öhm ... "Tomatencremesuppe" ... sieht ziemlich fleischig aus. Denk am besten gar nicht darüber nach, was wirklich hier drin sein könnte.',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_FOOD,
	);	

	protected static $weight = 5;

    protected function hid() {
        return parent::hid()
            ->add_action('Essen', Model_Action::factory()
                ->effect(
                    Model_Effect::factory()
                        ->effect(Model_Status::MS_STAT_HUNGER, 15)
                        ->effect(Model_Status::MS_STAT_THIRST, 10)
                        ->consume($this)
                        ->achieve(Model_Achievement::MA_BODY_EATER)
                        ->message('Anscheinend besteht diese Suppe aus einer seltenen Art von Tomaten, die fast genau so schmecken wie in Blut eingelegtes Menschenfleisch. Zumindest hast du dir also erfolgreich selbst eingeredet, dass es solche Tomaten wirklich gibt...')
                )
            );
    }
}	