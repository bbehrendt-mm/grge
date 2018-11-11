<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Bmt2 extends Model_Items_Bmt {

	protected static $static_info = Array(
			'name' => 'Bauchmuskeltrainer H.U.L.K. Pro',
			'icon' => 'bmt2',
			'description' => 'Ein gewöhnlicher Bauchmuskeltrainer ist für Flaschen! Dieser mit vergoldeten Kontakten ausgerüstete Bauchmuskeltrainer macht dir wirklich Dampf - aufgrund der hohen Spannung ist das übrigens wörtlich zu nehmen...',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_GEAR,
	);

    protected static $energy_base = 40;
	public static $health_list = Array(1,2,3,5,8,13,21,34,55,89);
	
	public function description() {
		return  parent::description() . ($this->power >= count(static::$health_list) ? '<b>Die Kontakte dieses Geräts sind etwas angekokelt... normale Batterien werden hier wohl nicht mehr funktionieren.</b>'
                : '');
	}

    protected function hid() {
        return parent::hid()
            ->add_action('Supercharger verwenden', Model_Action::factory()
                ->deny_for(Interface_Plentity::IC_NPC_ANIMAL)
                ->requirement(Model_Items_Generic_Supercharger::cls(), 1)
                ->effect(
                    Model_Effect::factory()
                        ->effect(Model_Status::MS_STAT_HEALTH, -85)
                        ->effect(Model_Status::MS_STAT_ENERGY, 100)
                        ->remove(Model_Items_Generic_Supercharger::cls(), 1)
                        ->message('Das war so ziemlich das schmerzhafteste, was du in den letzten 2 Stunden getan hast. Wenigstens hat sich deine Energie wieder aufgeladen...')
                )
            );
    }
}	