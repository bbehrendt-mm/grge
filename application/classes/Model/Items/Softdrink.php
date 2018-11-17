<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Softdrink extends Model_Items_Abstract_Item implements Interface_Static {

	protected static $static_info = Array(
			'name' => 'Softdrink',
			'icon' => 'softdrink/cola',
			'description' => 'Als Durstlöscher nicht ganz so effizient wie einfaches Wasser, aber man nimmt ja was man kriegen kann. Im Notfall auch verwendbar als Superkleber, Abführmittel und Ätzmittel.',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_FOOD,
	);
	
	protected static $instances_info = Array(
			Array(	'name' => 'Limonade',
					'icon' => 'softdrink/lemonade'),
			Array(	'name' => 'Bubble Tea',
					'icon' => 'softdrink/bubble'),
			Array(	'name' => 'Cola',
					'icon' => 'softdrink/cola'),
            Array(	'name' => 'Zuckerwasser',
                    'icon' => 'softdrink/sugar'),
	);

	protected static $weight = 1;

    protected function hid(): Model_Hid {
        return parent::hid()
            ->add_action('Trinken', Model_Action::factory()
                ->effect(
                    Model_Effect::factory()
                        ->effect(Model_Status::MS_STAT_ENERGY, 5)
                        ->effect(Model_Status::MS_STAT_THIRST, 10)
                        ->effect(Model_Status::MS_STAT_HEALTH, -1)
                        ->consume($this)
                        ->message('Ahhh, erfrischend. Das lindert deinen Durst, und du bekommst sogar ein wenig neue Energie.')
                )
            );
    }
}	