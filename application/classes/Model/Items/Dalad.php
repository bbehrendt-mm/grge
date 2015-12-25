<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Dalad extends Model_Items_Abstract_Item implements Interface_Static {

	protected static $static_info = Array(
			'name' => 'Dalad Jelly',
			'icon' => 'dalad',
			'description' => '... WAS ZUM TEUFEL IST "DALAD JELLY"???',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_FOOD,
	);
	
	protected static $weight = 3;

    protected function hid() {
        global $player;
        return parent::hid()
            ->add_action('... Essen?', Model_Action::factory()
                    ->effect(
                        Model_Effect::factory()
                            ->effect(Model_Status::MS_STAT_SLEEPY, 5)
                            ->effect(Model_Status::MS_STAT_ENERGY, 5)
                            ->effect(Model_Status::MS_STAT_HUNGER, 5)
                            ->effect(Model_Status::MS_STAT_THIRST, 5)
                            ->effect(Model_Status::MS_STAT_HEALTH, 5)
                            ->effect(Model_Status::MS_STAT_RADIATION, 15)
                            ->consume($this)
                            ->achieve(Model_Achievement::MA_DALAD)
                            ->message('Du hast Dalad Jelly gegessen... Herzlichen Glückwunsch?')
                    )
            )
            ->add_action('Jmd. verabreichen',
                Model_Action::factory()
                    ->effect(
                        Model_Effect::factory()
                            ->consume($this)
                            ->message('Du hast deinem Freund Dalad Jelly gegeben... was immer das auch sein mag.')
                        , null, null,
                        Model_Effect::factory()
                            ->effect(Model_Status::MS_STAT_SLEEPY, 5)
                            ->effect(Model_Status::MS_STAT_ENERGY, 5)
                            ->effect(Model_Status::MS_STAT_HUNGER, 5)
                            ->effect(Model_Status::MS_STAT_THIRST, 5)
                            ->effect(Model_Status::MS_STAT_HEALTH, 5)
                            ->effect(Model_Status::MS_STAT_RADIATION, 15)
                            ->achieve(Model_Achievement::MA_DALAD)
                            ->message(':name hat dir etwas Dalad Jelly verabreicht... Herzlichen Glückwunsch?', array(':name' => $player->name()))
                    )
            );
    }
}	