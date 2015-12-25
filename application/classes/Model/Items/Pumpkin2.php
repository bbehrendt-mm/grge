<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Pumpkin2 extends Model_Items_Abstract_Item implements Interface_Static {

	protected static $static_info = Array(
			'name' => 'Verfaulter Kürbis',
			'icon' => 'pumpkinbad',
			'description' => 'Das war mal ein essbarer Kürbis... naja, essen könntest du ihn immer noch, und vermutlich würde er deinen Hunder stillen - vorrausgesetzt, er bleibt in deinem Magen.',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_FOOD,
	);	

	protected static $weight = 14;

    protected function hid() {
        return parent::hid()
            ->add_action('Essen', Model_Action::factory()
                    ->effect(
                        Model_Effect::factory()
                            ->effect(Model_Status::MS_STAT_HUNGER, 80)
                            ->effect(Model_Status::MS_STAT_ENERGY, -10)
                            ->effect(Model_Status::MS_STAT_HEALTH, -45)
                            ->consume($this)
                            ->message('Dieser Kürbis lässt sich relativ leicht öffnen. Mit geschlossenen Augen und zuhealtener Nase lässt er sich zudem sogar fast leicht essen.')
                    )
            );
    }
}	