<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Generator extends Model_Items_Abstract_Item implements Interface_Static {

	protected static $static_info = Array(
			'name' => 'Portabler Generator',
			'icon' => 'generator',
			'description' => 'Allein der Duft dieses leckeren Plätzchens lässt dich alles um dich herum vergessen. Plötzlich bist du wieder ein kleines Kind, das unter dem Weihnachtsbaum sitzt und seine Geschenke auspackt. Natürlich kannst du dieses Plätzchen einfach essen - du könntest es natürlich auch in deinem Versteck für den Weihnachtsmann zurücklassen, der dir dafür sicherlich dankbar wäre...',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_GEAR,
	);

	protected static $weight = 50;

    protected function hid(): Model_Hid {
        return parent::hid()

            ->add_action('1 mAh mit Muskelkraft erzeugen', Model_Action::factory()
                ->requirement(Model_Status::MS_STAT_ENERGY, 60)
                ->effect(
                    Model_Effect::factory()
                        ->spawn(Model_Items_Energy::cls(), 1)
                        ->message('Es werde Licht! Herzlichen Glückwunsch, du hast etwas Strom für dein Versteck erzeugt!')
                )
            )
            ->add_action('5 mAh aus Batterien erzeugen', Model_Action::factory()
                ->requirement(Model_Items_Battery::cls(), 12)
                ->effect(
                    Model_Effect::factory()
                        ->spawn(Model_Items_Energy::cls(), 5)
                        ->message('Es werde Licht! Herzlichen Glückwunsch, du hast etwas Strom für dein Versteck erzeugt!')
                )
            )
            ->add_action('8 mAh aus Benzin erzeugen', Model_Action::factory()
                ->requirement(Model_Items_Generic_Jerrycan::cls(), 1)
                ->effect(
                    Model_Effect::factory()
                        ->spawn(Model_Items_Energy::cls(), 8)
                        ->message('Es werde Licht! Herzlichen Glückwunsch, du hast etwas Strom für dein Versteck erzeugt!')
                )
            )
            ;
    }
}	