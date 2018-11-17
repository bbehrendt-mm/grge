<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Xmas_Meat extends Model_Items_Abstract_Item implements Interface_Static, Interface_Event {

	protected static $static_info = Array(
			'name' => 'Weihnachtsessen',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_FOOD,
	);

    protected static $instances_info = Array(
        Array(	'name' => 'Mutzbraten',
            'icon' => 'xmas/meat1',
            'description' => 'Eine lokale Köstlichkeit aus Sachen und Thüringen, die auf keinem Weihnachtsmarkt fehlen darf!'),
        Array(	'name' => 'Thüringer Bratwurst',
            'icon' => 'xmas/meat2',
            'description' => '... sieht zumindest so ähnlich aus wie eine Thüringer Bratwurst. Die wirkliche Herkunft dieser Wurst wird wohl für immer ein Mysterium bleiben...'),
        Array(	'name' => 'Schaschlik',
            'icon' => 'xmas/meat3',
            'description' => 'Früher hast du immer gerne versucht zu erraten, ob das Fleisch auf deinem Spieß vom Rind, Schwein oder Lamm stammt... nun, woraus dieses Schaschlik gemacht ist willst du lieber nicht erraten.'),
    );

	protected static $weight = 3;

    protected function hid(): Model_Hid {
        return parent::hid()
            ->add_action('Essen', Model_Action::factory()
                    ->effect(
                        Model_Effect::factory()
                            ->effect(Model_Status::MS_STAT_HUNGER, 15)
                            ->effect(Model_Status::MS_STAT_FREEZE, -20)
                            ->consume($this)
                            ->message('Aah, das war gut. Und es wärmt schön von innen.')
                    )
            );
    }
}	