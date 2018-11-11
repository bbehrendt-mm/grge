<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Powderpack extends Model_Items_Abstract_Item implements Interface_Static {
	
	protected static $static_info = Array(
			'name' => '"Puderzucker"',
			'icon' => 'powderpack',
			'description' => 'Dieser ganz spezielle "Puderzucker" enthält wertvolle "Nährstoffe", die dir verlorene Energie sofort zurückbringen. Nebenwirkungen treten nur gaaanz selten auf und sind meistens noch nicht mal tödlich!',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_DRUG,
	);

	protected static $weight = 1;

    protected function hid() {
        $child_effects = !Globals::shadowPlayerExists() && Globals::PrimaryPlayerF()->job(1080);

        return parent::hid()
            ->add_action('"Verwenden"', Model_Action::factory()
                ->effect(
                    Model_Effect::factory()
                        ->effect(Model_Status::MS_STAT_HEALTH, -40 * ($child_effects ? 2 : 1))
                        ->effect(Model_Status::MS_STAT_DRUNK, 25)
                        ->buff($child_effects ? 'Model_Buffs_Exited' : null,false,10)
                        ->effect(Model_Status::MS_STAT_ENERGY, 40)
                        ->achieve(Model_Achievement::MA_PILL_EATER)
                        ->consume($this)
                        ->message('Wow, dieser "Puderzucker" hats echt in sich! Du fühlst dich als könntest du Bäume ausreissen! Das extreme Nasenbluten ist jedoch etwas nervig...')
                )
            );
    }
}	