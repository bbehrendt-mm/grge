<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Vedge4 extends Model_Items_Abstract_Item implements Interface_Static {
	
	protected static $static_info = Array(
			'name' => 'Vertrocknete Mutationsmelone',
			'icon' => 'vedge4',
			'description' => 'Das Ding sieht so aus, als wäre es vor 1000 Jahren mumifiziert worden - und genau so wird es wahrscheinlich auch schmecken.',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_FOOD,
	);

	protected static $weight = 1;

    protected function hid() {
        return parent::hid()
            ->add_action('Essen', Model_Action::factory()
                    ->effect(
                        Model_Effect::factory()
                            ->effect(Model_Status::MS_STAT_HUNGER, 5)
                            ->consume($this)
                            ->message('Von der Konsistenz her erinnert diese Mutationsmelone eher an einen Keks als an eine Frucht. Vom Geschmack her erinnert sie an einen Sack voll Staub. Wenigstens um Gewichtszunahme musst du dir keine Gedanken machen ...')
                    )
            );
    }
}	