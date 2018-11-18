<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Petfood extends Model_Items_Abstract_Item implements Interface_Static {

	protected static $static_info = Array(
			'name' => 'Tierfutter',
			'icon' => 'petfood',
			'description' => 'Das sieht ... lecker aus. Bist du sicher, dass du das Zeug nicht vielleicht für deine vierbeinigen Freunde aufheben möchtest?',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_FOOD,
	);

	protected static $weight = 1;

    protected function hid(): Model_Hid {
    return parent::hid()
        ->add_action('Essen', Model_Action::factory()
            ->allow_for(Interface_Plentity::IC_NPC_NONPC)
            ->effect(
                Model_Effect::factory()
                    ->effect(Model_Status::MS_STAT_HUNGER, 5)
                    ->consume($this)
                    ->message('Naja, so richtig gut hat das jetzt nicht geschmeckt... aber wer wird in der Postapokalypse schon wählerisch sein, nicht wahr?')
            )
        )
        ->add_action('Fressen', Model_Action::factory()
            ->allow_for(Interface_Plentity::IC_NPC_ANIMAL)
            ->effect(
                Model_Effect::factory()
                    ->effect(Model_Status::MS_STAT_HUNGER, 20)
                    ->consume($this)
            )
        );
    }
	
	public function mixchem($chemval): bool
    {
		$this->consume();
        switch ($chemval)
        {
            case 1:
                Tool_Scripts::chem_reaction(
                    'Du tunkst das Essen in die Chemikalie - und beginnt zu blubbern verfärbt sich! Das sieht... nicht mehr allzu lecker aus.',
                    $chemval,$this, new Model_Items_Petfood3());
                return false;
            case 3:case 4:case 5:
                Tool_Scripts::chem_reaction(
                    'Du tunkst das Essen in die Chemikalie - und beginnt zu blubbern und löst sich vor deinen Augen auf! So ein Ärger, das wirst du wohl nicht mehr essen können...',
                    $chemval,$this);
                return false;
            case 6:
                Tool_Scripts::chem_reaction(
                    'Du tunkst das Essen in die Chemikalie - und beginnt zu blubbern und zu glühen. Ob man das noch essen kann ...?',
                    $chemval,$this, new Model_Items_Petfood2());
                return true;
            case 10:
                Tool_Scripts::chem_reaction(
                    'Du tunkst das Essen in die Chemikalie - und beginnt zu blubbern und löst sich vor deinen Augen in seine Bestandteile auf! Zurück bleibt nur eine große glibbrige Masse Nährschleim. Lecker ...',
                    $chemval,$this, [new Model_Items_Nutrient,new Model_Items_Nutrient]);
                return true;
            default:
                Tool_Scripts::chem_reaction(
                    'Du tunkst das Essen in die Chemikalie - und beginnt zu blubbern und löst sich vor deinen Augen in seine Bestandteile auf! Zurück bleibt nur eine kleine glibbrige Masse Nährschleim. Lecker ...',
                    $chemval,$this, new Model_Items_Nutrient);
                return true;
        }
	}

}	