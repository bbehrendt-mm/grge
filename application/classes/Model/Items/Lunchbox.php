<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Lunchbox extends Model_Items_Abstract_Stackable {

	protected static $static_info = Array(
			'name' => 'Lunchbox',
			'icon' => 'lunchbox',
			'description' => 'Diese Lunchbox enthält mehrere Rationen einer auf optimale Nährstoffversorgung in der Postapokalypse abgestimmten Mahlzeit (auch bekannt als "belegte Brote").',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_FOOD,
	);

	protected static $weight = 9;
	
	protected static $max_size = 5;
	protected static $autospawn = Array(5,5);
	protected static $autoappender = Array('Ration', 'Rationen');

    protected function hid(): Model_Hid {
        return parent::hid()
            ->add_action('Eine Ration essen', Model_Action::factory()
                ->effect(
                    Model_Effect::factory()
                        ->effect(Model_Status::MS_STAT_HUNGER, 25)
                        ->effect(Model_Status::MS_STAT_ENERGY, 8)
                        ->consume($this)
                        ->message('Es geht doch nichts über belegte Brote. Dein Hunger ist gestillt und du fühlst neue Kraft. ' . (($this->count > 2) ? 'Jetzt sind noch :num Rationen in der Box.' : (($this->count === 2) ? 'Du hast deine Nahrungsrationen fast aufgebraucht. Eine Ration befindet sich noch in der Box.' : 'Die Box ist leer!')), array(':num' => $this->count - 1))
                )
            );
    }
}	