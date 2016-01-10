<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Fastfood extends Model_Items_Abstract_Item implements Interface_Static {

	protected static $static_info = Array(
			'name' => 'Fastfood',
			'icon' => 'fastfood/generic',
			'description' => '',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_FOOD,
	);
	
	protected static $instances_info = Array(
			Array(	'name' => 'Burger',
					'icon' => 'fastfood/burger',
					'description' => 'Der neue XXL TripleBigBaconExtraCheese! Dieser Bürger komprimiert die Kalorien von 60.000 Mahlzeiten in einen einzigen Burger! Und mit der neuen, verbesserten Rezeptur führt der erste Biss nur noch in 100/101 Fällen zu sofortigem Herzversagen!'),
			Array(	'name' => 'Salat "Fettfried"',
					'icon' => 'fastfood/salad',
					'description' => 'Heureka! Den besten Wissenschaftlern diesseits des Rubikon haben es geschafft! Dieser Salat verbindet ein gesund aussehendes Äußeres mit gigantischem Fettgehalt. Dieses Ding hilft nun zwar nicht unbedingt beim Abnehmen, aber es sorgt für ein gutes Gefühl - beim Zunehmen.'),
			Array(	'name' => 'Portion Pommes Rot-Weiß',
					'icon' => 'fastfood/fries',
					'description' => 'Der absolute Klassiker - Pommes Frittes, auch bekannt als die Einstiegsdroge zur Fettleibigkeit.'),
			Array(	'name' => 'Chicken Nuggets',
					'icon' => 'fastfood/chicken',
					'description' => 'Diese Chicken Nuggets bestehen zu 0.1% aus echtem Hühnchenfleisch und zu 99.9% aus dem, was in den letzten Tagen so auf der Autobahn überfahren wurde.'),
            Array(	'name' => 'Shrimps',
                    'icon' => 'fastfood/shrimp',
                    'description' => 'Du hast Shrimps gefunden - die Kartoffelchips des Meeres. Wie wir alle wissen ist Seafood auch ungekühlt praktisch unbegrenzt haltbar, du musst dir also keine Sorgen darüber machen, dass dieser Teller monatelang in der Sonne lag.'),
	);
	
	protected static $weight = 1;

    protected function hid() {
        return parent::hid()
            ->add_action('Essen', Model_Action::factory()
                ->effect(
                    Model_Effect::factory()
                        ->effect(Model_Status::MS_STAT_HUNGER, 30)
                        ->effect(Model_Status::MS_STAT_ENERGY, -5)
                        ->effect(Model_Status::MS_STAT_HEALTH, -1)
                        ->consume($this)
                        ->message('Das war lecker. Leider fühlst du dich jetzt etwas aufgepumpt, und verlierst etwas Energie.')
                )
            );
    }
	
	public function mixchem($chemval) {
        $this->consume();
        switch ($chemval)
        {
            case 4:case 5:case 6:case 7:case 8:case 9:
                Tool_Scripts::chem_reaction(
                    'Du tunkst das Essen in die Chemikalie - und beginnt zu blubbern und löst sich vor deinen Augen auf! So ein Ärger, das wirst du wohl nicht mehr essen können...',
                    $chemval,$this);
                return false;
            case 12:
                Tool_Scripts::chem_reaction(
                    'Du tunkst das Essen in die Chemikalie - und beginnt zu blubbern und löst sich vor deinen Augen in seine Bestandteile auf! Zurück bleibt nur eine große glibbrige Masse Nährschleim. Lecker ...',
                    $chemval,$this, new Model_Items_Nutrient2);
                return true;
            default:
                Tool_Scripts::chem_reaction(
                    'Du tunkst das Essen in die Chemikalie - und beginnt zu blubbern und löst sich vor deinen Augen in seine Bestandteile auf! Zurück bleibt nur eine große glibbrige Masse Nährschleim. Lecker ...',
                    $chemval,$this, new Model_Items_Nutrient);
                return true;
        }
	}
}	