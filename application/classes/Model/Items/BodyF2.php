<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_BodyF2 extends Model_Items_Abstract_Item implements Interface_Static {

	protected static $static_info = Array(
			'name' => 'Gefrorener Zombie',
			'icon' => 'body_f2',
			'description' => 'Das einzige, was dieser Zombie noch bedrohen kann, ist deine Darmflora.',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_FOOD,
            'deco' => 0,
	);
	
	protected static $weight = 70;

    protected function hid(): Model_Hid {
        return parent::hid()
            ->add_action('Fressen', Model_Action::factory()
                    ->effect(
                        Model_Effect::factory()
                            ->effect(Model_Status::MS_STAT_HUNGER, 50)
                            ->effect(Model_Status::MS_STAT_THIRST,   5)
                            ->effect(Model_Status::MS_STAT_HEALTH, -40)
                            ->consume($this)
                            ->achieve(Model_Achievement::MA_BODY_EATER)
                            ->spawn(Model_Items_Generic_Bone3::cls())
                            ->message('Nachdem du das runtergeschlungen hast dreht sich dir der Magen um - aber wenigstens ist er wieder voll. Hoffentlich ist deine Hausapotheke das auch ...')
                    )
            );
    }

	public function mixchem($chemval): bool
    {
        switch ($chemval)
        {
            case 1:
                $this->consume();
                Tool_Scripts::chem_reaction(
                    'Wenige Sekunden nachdem du die Chemikalie über den gefrorenen gegossen hast, beginnt er zu dampfen und aufzutauen!',
                    $chemval,$this, [new Model_Items_Body()]);
                return true;
            case 8:
                $this->consume();
                Tool_Scripts::chem_reaction(
                    'Die Chemikalie ätzt der Leiche das Fleisch von den Knochen! Es bleibt ledigtlich etwas Blut zurück... und ein perfekt erhaltenes Skelett!',
                    $chemval,$this, [new Model_Items_Generic_Bone3,new Model_Items_Generic_Waterb,new Model_Items_Generic_Waterb]);
                return true;
            default:
                Tool_Scripts::chem_reaction(
                    'Die Chemikalie perlt einfach an dem gefrorenen Körper ab ...',
                    $chemval,$this);
                return false;
        }
	}
}	