<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Cookie extends Model_Items_Abstract_Item implements Interface_Static {

	protected static $static_info = Array(
			'name' => 'Weihnachtsplätzchen',
			'icon' => 'cookie',
			'description' => 'Allein der Duft dieses leckeren Plätzchens lässt dich alles um dich herum vergessen. Plötzlich bist du wieder ein kleines Kind, das unter dem Weihnachtsbaum sitzt und seine Geschenke auspackt. Natürlich kannst du dieses Plätzchen einfach essen - du könntest es natürlich auch in deinem Versteck für den Weihnachtsmann zurücklassen, der dir dafür sicherlich dankbar wäre...',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_FOOD,
	);

	protected static $weight = 0.2;

    protected function hid() {
        return parent::hid()
            ->add_action('Essen', Model_Action::factory()
                ->effect(
                    Model_Effect::factory()
                        ->effect(Model_Status::MS_STAT_HUNGER, 5)
                        ->consume($this)
                        ->message('Der gebackene Teig zerläuft in deinem Mund, und du bist angefüllt mir Weihnachtsgefühlen. Wie wunderbar!')
                )
            );
    }
	
	public function mixchem($chemval) {
        $this->consume();
        switch ($chemval)
        {
            case 4:case 5:case 6:case 8:case 10:case 12:
                Tool_Scripts::chem_reaction(
                    'Du tunkst das Plätzchen in die Chemikalie - und beginnt zu blubbern und löst sich vor deinen Augen auf! So ein Ärger, das wirst du wohl nicht mehr essen können...',
                    $chemval,$this);
                return false;
            default:
                Tool_Scripts::chem_reaction(
                    'Du tunkst das Plätzchen in die Chemikalie - und beginnt zu blubbern, während die Chemikalie langsam vom Plätzchenteig aufgenommen wird. Ob du dieses Plätzchen noch .... essen kannst?',
                    $chemval,$this, new Model_Items_Cookie2);
                return true;
        }
	}

}	