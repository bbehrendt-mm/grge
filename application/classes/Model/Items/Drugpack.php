<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Drugpack extends Model_Items_Abstract_Item implements Interface_Static {
	
	protected static $static_info = Array(
			'name' => 'Mysteriöse Kiste',
			'icon' => 'drugpack',
			'description' => 'Diese Kiste sieht nicht so aus als wäre sie für den regulären Handel bestimmt... du könntest sie öffnen und schauen, was drin ist.',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_MISC,
	);

	protected static $weight = 20;

    protected function hid() {
        return parent::hid()
            ->add_action('Aufbrechen', Model_Action::factory()
                ->deny_for(Interface_Plentity::IC_NPC_ANIMAL)
                ->requirement(Model_Status::MS_STAT_ENERGY, 10)
                ->effect(Model_Effect::factory()
                    ->message('Mit etwas Mühe bekommst du die Kiste aufgebrochen - und stellst fest dass sie leer ist. Na toll ...')
                    ->consume($this)
                )
                ->effect(Model_Effect::factory()
                    ->message('Mit etwas Mühe bekommst du die Kiste aufgebrochen - und stellst fest dass sie randvoll mit bunten Pillen ist!')
                    ->spawn('Model_Items_Pill', 12)
                    ->consume($this)
                )
                ->effect(Model_Effect::factory()
                    ->message('Mit etwas Mühe bekommst du die Kiste aufgebrochen - und stellst fest dass sie randvoll mit Medikamenten ist!')
                    ->spawn('Model_Items_Paracetin', 2)
                    ->spawn('Model_Items_Paracetoid', 2)
                    ->spawn('Model_Items_Paralaxium', 2)
                    ->spawn(new Model_Items_Twinoid(10))
                    ->consume($this)
                )
                ->effect(Model_Effect::factory()
                    ->message('Mit etwas Mühe bekommst du die Kiste aufgebrochen - und stellst fest dass sie randvoll mit Chemikalien ist!')
                    ->spawn('Model_Items_Chem', 5)
                    ->spawn(new Model_Items_Chem(6))
                    ->consume($this)
                )
                ->effect(Model_Effect::factory()
                    ->message('Mit etwas Mühe bekommst du die Kiste aufgebrochen - und stellst fest dass sie randvoll mit Päckchen voller weißem Puder ist. Leider hast du ein paar Päckchen beim Aufbrechen der Kiste beschädigt, sodass sich deren Inhalt in Form einer Wolke um dich verbreitet...')
                    ->spawn('Model_Items_Powderpack', 6)
                    ->effect(Model_Status::MS_STAT_DRUNK, 30)
                    ->effect(Model_Status::MS_STAT_ENERGY, 30)
                    ->effect(Model_Status::MS_STAT_HEALTH, -50)
                    ->consume($this)
                )
            );
    }
}	