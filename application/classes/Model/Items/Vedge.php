<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Vedge extends Model_Items_Abstract_Item implements Interface_Static {

	protected static $static_info = Array(
			'name' => 'Saftige Mutationsmelone',
			'icon' => 'vedge',
			'description' => 'Diese Mutationsmelone sieht prächtig aus! Sie ist knallrot, saftig, und ihre Tentakel versuchen nur ganz selten, dich zu erwürgen.',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_FOOD,
	);

	protected static $weight = 3;

    protected function hid() {
        return parent::hid()
            ->add_action('Essen', Model_Action::factory()
                ->effect(
                    Model_Effect::factory()
                        ->effect(Model_Status::MS_STAT_HUNGER, 25)
                        ->effect(Model_Status::MS_STAT_THIRST, 25)
                        ->consume($this)
                        ->message('Der Saft spritzt dir nur so ins Gesicht, als du in diese Mutationsmelone beißt. So etwas gutes hast du wirklich lange nicht mehr gegessen!')
                )
            );
    }
}	