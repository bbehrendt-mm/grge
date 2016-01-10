<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Nutrient2 extends Model_Items_Abstract_Item implements Interface_Static {

	protected static $static_info = Array(
			'name' => 'Nährplasma',
			'icon' => 'nutrient2',
			'description' => 'Dieses Zeug ist vollgepumpt mit Chemie. Was diese Chemie bewirkt? Who knows?',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_FOOD,
	);

	protected static $weight = 5;

    protected function hid() {
        return parent::hid()
            ->add_action('Verschlingen', Model_Action::factory()
                ->show_as(
                    Model_Effect::factory()
                        ->effect(Model_Status::MS_STAT_HUNGER, 30)
                        ->ambiguous_effect()
                        ->buff('Model_Buffs_Drug1', false, 12)
                        ->consume($this)
                        ->achieve(Model_Achievement::MA_PILL_EATER)
                , null, true)
                ->effect(
                    Model_Effect::factory()
                        ->effect(Model_Status::MS_STAT_ENERGY, 100)
                        ->message('Du spürst ein leichtes kribbeln in der Kehle... gefolgt von einem unglaublichen Energieschub!')
                )
                ->effect(
                    Model_Effect::factory()
                        ->effect(Model_Status::MS_STAT_HEALTH, 100)
                        ->message('Du spürst ein leichtes kribbeln in der Kehle... plötzlich spürst du, wie sich dein Körper entspannt und deine Wunden im Rekordtempo heilen!')
                )
                ->effect(
                    Model_Effect::factory()
                        ->effect(Model_Status::MS_STAT_SLEEPY, 100)
                        ->effect(Model_Status::MS_STAT_DRUNK, -100)
                        ->message('Du spürst ein leichtes kribbeln in der Kehle... und mit einem Mal klärt sich dein Kopf auf und du bist geistig wieder total fit!')
                )
                ->effect(
                    Model_Effect::factory()
                        ->effect(Model_Status::MS_STAT_ENERGY, -50)
                        ->effect(Model_Status::MS_STAT_HEALTH, -50)
                        ->effect(Model_Status::MS_STAT_DRUNK, 50)
                        ->message('Du spürst ein leichtes kribbeln in der Kehle... gefolgt von einem unglaublichen Elendsgefühl! Du hättest das Zeug nicht essen dürfen ...')
                )
            );
    }
}	