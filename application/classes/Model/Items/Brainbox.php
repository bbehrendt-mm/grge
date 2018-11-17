<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Brainbox extends Model_Items_Abstract_Stackable {

	protected static $static_info = Array(
			'name' => 'Kiste mit Gehirnen',
			'icon' => 'brainbox',
			'description' => 'Diese Kiste enthält... nun... GEHIRNE! Wer zum Teufel verpackt denn bitte GEHIRNE in KISTEN? Und... warum?',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_FOOD,
	);

	protected static $weight = 15;
	
	protected static $max_size = 6;
	protected static $autospawn = Array(4,4);
	protected static $autoappender = Array('Gehirn', 'Gehirne');

    protected function hid(): Model_Hid {
        return parent::hid()
            ->add_action('Ein Gehirn essen', Model_Action::factory()
                    ->effect(
                        Model_Effect::factory()
                            ->effect(Model_Status::MS_STAT_HUNGER, 21)
                            ->effect(Model_Status::MS_STAT_ENERGY, 3)
                            ->effect(Model_Status::MS_STAT_HEALTH, -11)
                            ->consume($this)
                            ->message('Oh mein Gott, du hast tatsächlich eins der Gehirne GEGESSEN? ' . (($this->count > 2) ? 'Jetzt sind noch :num Gehirne in der Kiste.' : (($this->count == 2) ? 'Ein Gehirn befindet sich noch in der Kiste.' : 'Die Kiste ist leer!')), array(':num' => $this->count - 1))
                    )
            );
    }
}	