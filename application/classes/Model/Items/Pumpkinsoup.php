<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Pumpkinsoup extends Model_Items_Abstract_Item implements Interface_Static {

	protected static $static_info = Array(
			'name' => 'Kürbissuppe',
			'icon' => 'pumpkinsoup',
			'description' => 'Diese leckere Suppe stillt Hunger und Durst - und verbreitet ein herbstliches Aroma. Was will man mehr?',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_FOOD,
	);	

	protected static $weight = 5;

    protected function hid() {
        return parent::hid()
            ->add_action('Essen', Model_Action::factory()
                    ->effect(
                        Model_Effect::factory()
                            ->effect(Model_Player::MP_STAT_HUNGER, 25)
                            ->effect(Model_Player::MP_STAT_THIRST, 25)
                            ->consume($this)
                            ->message('Du schlürfst deine Schale Kürbissuppe als sei sie das beste, was du in letzter Zeit gegessen hast. Moment.... sie IST das beste, was du in letzter Zeit gegessen hast!')
                    )
            );
    }
}	