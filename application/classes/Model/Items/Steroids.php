<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Steroids extends Model_Items_Abstract_Item implements Interface_Static {

	protected static $static_info = Array(
			'name' => 'Steroide',
			'icon' => 'steroids',
			'description' => 'Du willst höchste sportliche Leistungen erreichen, ohne deine Zeit mit anstrengendem Training verschwenden zu müssen? Diese definitiv hygienisch einwandfreie Spritze, die du im Dreck liegend gefunden hast, wird dir dabei helfen.',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_DRUG,
	);
	
	protected static $weight = 1;

    protected function hid(): Model_Hid {
        return parent::hid()
            ->add_action('Applizieren', Model_Action::factory()
                ->deny_for(Interface_Plentity::IC_NPC_ANIMAL)
                ->allow_remote(false)
                ->allow_auto(false)
                ->effect(
                    Model_Effect::factory()
                        ->buff(Model_Buffs_Steroids::cls(), false, 24)
                        ->buff(Model_Buffs_Drug1::cls(), false, 48)
                        ->consume($this)
                        ->message('Innerhalb von Sekunden durchfließt Energie deine Muskeln und Steroide deine Adern.')
                )
            );
    }
}	