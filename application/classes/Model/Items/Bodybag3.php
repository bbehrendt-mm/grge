<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Bodybag3 extends Model_Items_Abstract_Item implements Interface_Static {

	protected static $static_info = Array(
			'name' => 'Prall gefüllter Leichensack',
			'icon' => 'bodybag3',
			'description' => 'Da hast du ja einen dicken Fang an Land gezogen. Zum Glück kannst du ihn in diesem Leichensack gut transportieren.',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_GEAR,
            'deco' => -10,
	);
	
	protected static $weight = 56;

    protected function hid() {
        return parent::hid()
            ->add_action('Leiche herausholen', Model_Action::factory()
                    ->requirement(Model_Status::MS_STAT_ENERGY, 5)
                    ->effect(
                        Model_Effect::factory()
                            ->consume($this)
                            ->spawn(new Model_Items_Body('Zerknautschte Leiche', 'Naja, die Form hat beim Transport im Leichensack etwas gelitten... Aber man erkennt, dass es mal so was ähnliches wie ein Mensch war!'))
                            ->spawn('Model_Items_Bodybag2')
                    )
            );
    }
}	