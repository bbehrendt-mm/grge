<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Bodycrisp extends Model_Items_Abstract_Item implements Interface_Static {
	
	protected static $static_info = Array(
			'name' => 'Frittierte Leiche',
			'icon' => 'bodyf',
			'description' => 'Wer sagt, dass man nur kleine Dinge frittieren kann? Diese Leiche kannst du jetzt fast ohne gesundheitliche Risiken essen, und sie schmeckt auch noch viel besser!',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_FOOD,
            'deco' => -20,
	);

	protected static $weight = 80;

    protected function hid(): Model_Hid {
        return parent::hid()
            ->add_action('Fressen', Model_Action::factory()
                    ->effect(
                        Model_Effect::factory()
                            ->effect(Model_Status::MS_STAT_HUNGER, 100)
                            ->effect(Model_Status::MS_STAT_HEALTH, -20)
                            ->consume($this)
                            ->achieve(Model_Achievement::MA_BODY_EATER)
                            ->spawn(Model_Items_Generic_Bone3::cls())
                            ->message('Äußerst delikat ... das Frittierfett ist zwar anscheinend etwas ranzig gewesen, und innen drin war die Leiche noch größtenteils roh - trotzdem eine der leckersten Mahlzeiten die du in letzter Zeit gehabt hast!')
                    )
            );
    }
	
	public function mixchem($chemval) {
        switch ($chemval)
        {
            case 5:
                $this->consume();
                Tool_Scripts::chem_reaction(
                    'Die Chemikalie ätzt die Kruste der Leiche weg! Es bleibt ledigtlich etwas Nährschleim zurück... und ein perfekt erhaltenes Skelett!',
                    $chemval,$this, [new Model_Items_Generic_Bone3,new Model_Items_Nutrient]);
                return true;
            case 10:
                $this->consume();
                Tool_Scripts::chem_reaction(
                    'Die Chemikalie löst die Leiche vollständig auf! Zurück bleibt nur eine ganze Menge Schleim...',
                    $chemval,$this, [new Model_Items_Nutrient,new Model_Items_Nutrient, new Model_Items_Nutrient2]);
                return true;
            default:
                Tool_Scripts::chem_reaction(
                    'Der frittierte Körper der Leiche saugt die Chemikalie auf, aber du kannst keine Veränderung feststellen ...',
                    $chemval,$this);
                return false;
        }
	}
}	