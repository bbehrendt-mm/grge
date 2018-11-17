<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Rawmeat3 extends Model_Items_Abstract_Item implements Interface_Static {

	protected static $static_info = Array(
			'name' => 'Knochen mit infiziertem Fleisch',
			'icon' => 'rawmeat3',
			'description' => 'Dieser Knochen ist noch ziemlich fleischig... riecht allerdings ungewöhnlich streng und hat eine für Fleisch merkwürdig grüne Färbung...',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_FOOD,
	);

	protected static $weight = 5;

    protected function hid(): Model_Hid {
        return parent::hid()
            ->add_action('Fressen', Model_Action::factory()
                ->effect(
                    Model_Effect::factory()
                        ->effect(Model_Status::MS_STAT_HUNGER, 15)
                        ->effect(Model_Status::MS_STAT_HEALTH, -15)
                        ->effect(Model_Status::MS_STAT_ZOMBIFY, 5)
                        ->consume($this)
                        ->spawn(Model_Items_Bone::cls())
                        ->message('Es schmeckt ein bisschen nach Hähnchen .... und zwar nach einem Hähnchen, dass 4 Wochen lang in der Sonne verwest ist, danach vond en Toten erweckt und mit einer rostigen Machete abgeschlachtet wurde!')
                )
            );
    }
	
	public function mixchem($chemval) {
        switch ($chemval)
        {
            case 5:
                $this->consume();
                Tool_Scripts::chem_reaction(
                    'Das Fleisch saugt die Chemikalie auf... irgendwie sieht das ganze jetzt ein wenig gesünder aus!',
                    $chemval,$this, new Model_Items_Rawmeat);
                return true;
            case 6:
                $this->consume();
                Tool_Scripts::chem_reaction(
                    'Die Chemikalie ätzt das gesamte Fleisch weg! Nur der Knochen bleibt zurück...',
                    $chemval,$this, new Model_Items_Bone);
                return true;
            default:
                Tool_Scripts::chem_reaction(
                    'Das Fleisch saugt die Chemikalie auf, aber du kannst keine Veränderung feststellen ...',
                    $chemval,$this);
                return false;
        }
	}
}	