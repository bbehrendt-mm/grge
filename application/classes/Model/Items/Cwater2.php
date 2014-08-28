<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Cwater2 extends Model_Items_Abstract_Item implements Interface_Static {

	protected static $static_info = Array(
			'name' => 'Dreckiges Kondenswasser',
			'icon' => 'cwater1',
			'description' => 'Du hast ein paar Tropfen Kondenswasser gesammelt... Leider war die Oberfläche nicht allzu sauber. Dieses Wasser ist vermutlich nicht übermäßig gesund...',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_FOOD,
	);
	
	protected static $weight = 1;

    protected function hid() {
        return parent::hid()
            ->add_action('Trinken', Model_Action::factory()
                    ->effect(
                        Model_Effect::factory()
                            ->effect(Model_Player::MP_STAT_THIRST, 5)
                            ->effect(Model_Player::MP_STAT_HEALTH, -5)
                            ->consume($this)
                            ->message('Trotz des ekligen geschmacks leckst du das Gefäß ab, um auch die letzten Tropfen Wasser noch in deinen Mund zu bekommen.')
                    )
            );
    }
}	