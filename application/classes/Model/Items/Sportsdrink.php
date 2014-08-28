<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Sportsdrink extends Model_Items_Abstract_Stackable {

	protected static $static_info = Array(
			'name' => 'Isotonisches Sportgetränk',
			'icon' => 'sportsdrink',
			'description' => 'Dieses Getränk enthält neben wertvollem Wasser auch allerlei coole Vitamine, Mineralien und sonstige Nährstoffe. Außerdem ist es nur ein ganz kleines bisschen belastet mit Schwermetallen.',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_FOOD,
	);	

	protected static $weight = 9;
	
	protected static $max_size = 5;
	protected static $autospawn = Array(5,5);
	protected static $autoappender = Array('Schluck', 'Schluck');

    protected function hid() {
        return parent::hid()
            ->add_action('Einen Schluck nehmen', Model_Action::factory()
                    ->effect(
                        Model_Effect::factory()
                            ->effect(Model_Player::MP_STAT_THIRST, 25)
                            ->effect(Model_Player::MP_STAT_ENERGY, 8)
                            ->consume($this)
                            ->message('Es geht doch nichts über das Prickeln in der Kehle nach dem Genuss dieses Sportgetränks. Dein Durst ist gestillt und du fühlst neue Kraft. ' . (($this->count > 2) ? 'Jetzt sind noch :num Schluck in der Flasche.' : (($this->count == 2) ? 'Du hast diesen Drink fast leergetrunken. Eine Schluck befindet sich noch in der Flasche.' : 'Die Flasche ist leer!')), array(':num' => $this->count - 1))
                    )
            );
    }
}	