<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Cyanide extends Model_Items_Abstract_Item implements Interface_Static {
	
	protected static $static_info = Array(
			'name' => 'Zyanid',
			'icon' => 'cyanide',
			'description' => 'Obwohl dieses kleine Ding die meisten deiner Probleme lösen kann - beim Erklimmen des Rankings hilft es nur bedingt.',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_DRUG,
	);
	
	protected static $instances_info = Array(
			Array(	'name' => 'Zyanid'),
			Array(	'name' => 'Selbstmord-Pille'),
			Array(	'name' => 'Exitus (1 Ration)'),
	);

	protected static $weight = 1;

    protected function hid(): Model_Hid {
        return parent::hid()
            ->add_action('Schlucken', Model_Action::factory()
                ->deny_for(Interface_Plentity::IC_NPC_ANIMAL)
                ->allow_remote(false)
                ->effect(
                    Model_Effect::factory()
                        ->buff('Model_Buffs_Heartbeat', true)
                        ->effect(Model_Status::MS_STAT_HUNGER, 1)
                        ->consume($this)
                        ->message('Alles ist so furchtbar! Überall Tod, Verderben, Leid, Zombies und RTL-Kamerateams! Tja, da kann die Hölle ja nicht wirklich viel schlimmer sein, also runter mit dem Zyanid!')
                )
            );
    }
}	