<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Vedge2 extends Model_Items_Abstract_Item implements Interface_Static {

	protected static $static_info = Array(
			'name' => 'Unreife Mutationsmelone',
			'icon' => 'vedge2',
			'description' => 'Ein bisschen hätte diese Mutationsmelone schon noch reifen können... sie ist ziemlich hart und grün, aber innen drin bestimmt trotzdem saftig!',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_FOOD,
	);

	protected static $weight = 3;

    protected function hid() {
        return parent::hid()
            ->add_action('Essen', Model_Action::factory()
                ->effect(
                    Model_Effect::factory()
                        ->effect(Model_Status::MS_STAT_HUNGER, 15)
                        ->effect(Model_Status::MS_STAT_THIRST, 15)
                        ->consume($this)
                        ->message('Tatsächlich ist diese Mutationsmelone ziemlich hart und zäh, außerdem schmeckt sie nach Erde. Nichtsdestotrotz stillt sie deinen Hunger und deinen Durst, also beschwer dich gefälligst nicht!')
                )
            );
    }
}	