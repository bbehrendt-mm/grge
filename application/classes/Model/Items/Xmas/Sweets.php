<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Xmas_Sweets extends Model_Items_Abstract_Item implements Interface_Static, Interface_Event {

	protected static $static_info = Array(
			'name' => 'Süssigkeiten',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_FOOD,
	);

    protected static $instances_info = Array(
        Array(	'name' => 'Schokolade',
            'icon' => 'xmas/swt1',
            'description' => 'Auch bekannt unter dem namen "Zucker mit Schokoladengeschmack". Kinder können sich das Zeug tonnenweise in den Mund schieben, leiden danach allerdings auch für Wochen an Verstopfung und Zahnschmerzen.'),
        Array(	'name' => 'Kandierte Nüsse',
            'icon' => 'xmas/swt2',
            'description' => 'Diese Süssigkeit weckt schlimme Erinnerungen an deine Kindheit, als dir ein fremder in einer dunklen Gasse seine "ganz speziellen" kandierten Nüsse zeigen wollte...'),
        Array(	'name' => 'Bonbons',
            'icon' => 'xmas/swt3',
            'description' => 'Diese Bonbons sind zwar immer noch Süss, leider auch steinhart... Ob du die wohl als Munition in deinem Revolver verwenden könntest?'),
    );

	protected static $weight = 2;

    protected function hid() {
        return parent::hid()
            ->add_action('Essen', Model_Action::factory()
                    ->effect(
                        Model_Effect::factory()
                            ->effect(Model_Status::MS_STAT_HUNGER, 5)
                            ->effect(Model_Status::MS_STAT_HEALTH, -3)
                            ->consume($this)
                            ->message('Aah, das war gut... aber woher kommt dieser Beigeschmack von aufgelösten Zähnen?')
                    )
            );
    }
}	