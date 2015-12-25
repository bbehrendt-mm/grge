<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Rawmeat extends Model_Items_Abstract_Item implements Interface_Static {

	protected static $static_info = Array(
			'name' => 'Knochen mit Fleisch',
			'icon' => 'rawmeat',
			'description' => 'An diesem Knochen hängt noch eine Menge Fleisch. Du könntest es essen... aber dafür müsstest du schon wirklich verzweifelt sein, oder?',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_FOOD,
	);

	protected static $weight = 5;

    protected function hid() {
        return parent::hid()
            ->add_action('Fressen', Model_Action::factory()
                    ->effect(
                        Model_Effect::factory()
                            ->effect(Model_Status::MS_STAT_HUNGER, 15)
                            ->effect(Model_Status::MS_STAT_HEALTH, -10)
                            ->consume($this)
                            ->spawn('Model_Items_Bone')
                            ->message('Es schmeckt ein bisschen nach Hähnchen .... und zwar nach einem Hähnchen, dass 4 Wochen lang in der Sonne verwest ist!')
                    )
            );
    }
	
	public function mixchem($chemval) {
        switch ($chemval)
        {
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