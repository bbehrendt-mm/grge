<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Nutrient extends Model_Items_Abstract_Item implements Interface_Static {

	protected static $static_info = Array(
			'name' => 'Nährschleim',
			'icon' => 'nutrient',
			'description' => 'Diese glibbrige Masse deckt den kompletten Tagesbedarf an Ekel sowie diversen Nährstoffen. Es kostet nur ein bisschen Überwindung ...',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_FOOD,
	);

	protected static $weight = 3;

    protected function hid(): Model_Hid {
        return parent::hid()
            ->add_action('Verschlingen', Model_Action::factory()
                ->effect(
                    Model_Effect::factory()
                        ->effect(Model_Status::MS_STAT_HUNGER, 30)
                        ->consume($this)
                        ->message('Das schmeckte wie ein geschmolzener Zombie, dessen Haltbarkeitsdatum abgelaufen ist ... aber zumindest stillt es deinen Hunger. Was will man mehr?')
                )
            );
    }
	
	public function mixchem($chemval) {
        $this->consume();
        switch ($chemval)
        {
            case 6:
                Tool_Scripts::chem_reaction(
                    'Du rührst die Chemikalie in den Nährschleim... es fängt an zu blubbern und der Schleim ändert seine Farbe. Ähm... lecker?',
                    $chemval,$this, new Model_Items_Nutrient2);
                return true;
            default:
                Tool_Scripts::chem_reaction(
                    'Du rührst die Chemikalie in den Nährschleim... zunächst geschieht nichts, doch dann beginnt der Schleim plötzlich zu verbrennen! Naja siehs mal so... jetzt musst du das Zeug wenigstens nicht mehr essen.',
                    $chemval,$this);
                return false;
        }
	}

}	