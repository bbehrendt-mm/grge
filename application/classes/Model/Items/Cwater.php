<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Cwater extends Model_Items_Abstract_Item implements Interface_Static {

	protected static $static_info = Array(
			'name' => 'Kondenswasser',
			'icon' => 'cwater1',
			'description' => 'Du hast ein paar Tropfen Kondenswasser gesammelt. Wirklich viel ist es nicht, aber immerhin besser als nichts!',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_FOOD,
	);
	
	protected static $weight = 1;

    protected function hid() {
        return parent::hid()
            ->add_action('Trinken', Model_Action::factory()
                    ->effect(
                        Model_Effect::factory()
                            ->effect(Model_Player::MP_STAT_THIRST, 5)
                            ->consume($this)
                            ->message('Du leckst das Gefäß gierig leer, um auch die letzten Tropfen Wasser noch in deinen Mund zu bekommen.')
                    )
            );
    }
}	