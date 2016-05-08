<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Morphine extends Model_Items_Abstract_Item implements Interface_Static {

	protected static $static_info = Array(
			'name' => 'Morphium',
			'icon' => 'morphine',
			'description' => 'Mit einer ordentlichen Dosis Morphium fühlt sich die Zombie-Apokalypse wie ein Ponyhof und ein abgerissener Arm wie ein kleiner Kratzer an. Deine Schmerztoleranz wird während der Wirkdauer massiv erhöht, allerdings musst du dafür erhöhte Müdigkeit sowie eine sofortige Drogenabhängigkeit in Kauf nehmen. Apropos Abhängigkeit: Von einer Überdosis Morphium ist allgemein eher abzuraten...',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_DRUG,
	);
	
	protected static $weight = 1;

    protected function hid() {
        return parent::hid()
            ->add_action('Applizieren', Model_Action::factory()
                ->deny_for(Interface_Plentity::IC_NPC_ANIMAL)
                ->allow_remote(false)
                ->allow_auto(false)
                ->effect(
                    Model_Effect::factory()
                        ->buff('Model_Buffs_Morphine', false, 144)
                        ->buff('Model_Buffs_Drug1', false, 144)
                        ->buff('Model_Buffs_Drug2')
                        ->consume($this)
                        ->message('Du spritzt dir eine Dosis Morphium. Deine Schmerzen verschwinden, doch dein ganzer Körper fühlt sich auf einmal taub an...')
                )
            );
    }
}	