<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Fleshfood extends Model_Items_Abstract_Item implements Interface_Static {

	protected static $static_info = Array(
			'name' => 'Eklige Fleischfetzen',
			'icon' => 'fleshfood/f3',
			'description' => 'Ein Haufen undefinierbarer Fleischfetzen. Es ist unmöglich zu erkennen, wovon sie stammen. Du kannst sie einfach herunterschlingen und hoffen, dass das mal ein Tier war... Oder du könntest kranke chemische Experimente damit anstellen.',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_FOOD,
	);
	
	protected static $instances_info = Array(
        Array(	'icon' => 'fleshfood/f1'),
        Array(	'icon' => 'fleshfood/f2'),
        Array(	'icon' => 'fleshfood/f3'),
	);
	
	protected static $weight = 0.2;

    protected function hid() {
        return parent::hid()
            ->add_action('Essen', Model_Action::factory()
                ->effect(
                    Model_Effect::factory()
                        ->effect(Model_Status::MS_STAT_HUNGER, 2)
                        ->effect(Model_Status::MS_STAT_HEALTH, -5)
                        ->consume($this)
                        ->message('Hm.... das hat ein bisschen wie Döner geschmeckt, nur ohne das Pferdefleisch.')
                )
            );
    }
	
	public function mixchem($chemval) {
        $a = mt_rand(1,6);
        $b = mt_rand(3,6);
        $c = mt_rand(7,12);
        $d = mt_rand(9,12);
		$this->consume();
		if ($chemval == $a || $chemval == $b || $chemval == $c || $chemval == $d) {
			Tool_Scripts::chem_reaction(
                'Du wirfst die Fetzen in ein Gefäß mit der Chemikalie... und wirst sofort von einem Lichtblitz geblendet! Die Fleischfetzen haben eine Seele freigesetzt!',
                $chemval, $this, ($chemval == max($a,$b) || $chemval == max($c,$d)) ? new Model_Items_Soul2() : new Model_Items_Soul()
            );
		} else {
            switch ($chemval) {
                case 1:
                    $item = new Model_Items_Generic_Waterb();
                    break;
                case 2:
                    $item = new Model_Items_Pill();
                    break;
                case 3:
                    $item = new Model_Items_Nutrient();
                    break;
                case 4:
                    $item = new Model_Items_Softdrink();
                    break;
                case 5:
                    $item = new Model_Items_Dalad();
                    break;
                case 6:
                    $item = new Model_Items_Nutrient2();
                    break;
                case 7:
                    $item = new Model_Items_Body();
                    break;
                case 8:
                    $item = new Model_Items_Body3();
                    break;
                case 9:
                    $item = [new Model_Items_Nutrient(), new Model_Items_Dalad()];
                    break;
                case 10:
                    $item = new Model_Items_Fastfood();
                    break;
                case 11:
                    $item = [new Model_Items_Pill(), new Model_Items_Pill()];
                    break;
                case 12:
                    $item = new Model_Items_Generic_Cloth();
                    break;
                default: return false;
            }

            Tool_Scripts::chem_reaction(
                'Du wirfst die Fetzen in ein Gefäß mit der Chemikalie... es blubbert und spritzt ein wenig. Als du in das Gefäß schaust, hat sich die Struktur der Fetzen komplett verändert!',
                $chemval, $this, $item
            );
		}
        return true;
	}
}	