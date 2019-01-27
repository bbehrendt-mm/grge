<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_BodyF3 extends Model_Items_Abstract_Item implements Interface_Static {

	protected static $static_info = Array(
			'name' => 'Gefrorenes Haustier',
			'icon' => 'body_f3',
			'description' => 'Das passiert, wenn sich jemand kein Tierfutter mehr leisten kann, sich aber trotzdem nicht von seinem Haustier trennen möchte ...',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_FOOD,
            'deco' => 2,
	);
	
	protected static $weight = 25;

    protected function hid(): Model_Hid {
        return parent::hid()
            ->add_action('Fressen', Model_Action::factory()
                    ->effect(
                        Model_Effect::factory()
                            ->effect(Model_Status::MS_STAT_HUNGER, 35)
                            ->effect(Model_Status::MS_STAT_THIRST,   5)
                            ->effect(Model_Status::MS_STAT_HEALTH, -10)
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
            case 2:
                $this->consume();
                Tool_Scripts::chem_reaction(
                    'Wenige Sekunden nachdem du die Chemikalie über den gefrorenen gegossen hast, beginnt er zu dampfen und aufzutauen!',
                    $chemval,$this, [new Model_Items_Body()]);
                return true;
            case 5:
                $this->consume();
                Tool_Scripts::chem_reaction(
                    'Die Chemikalie ätzt der Leiche das Fleisch von den Knochen! Es bleibt ledigtlich etwas Blut zurück... und ein perfekt erhaltenes Skelett!',
                    $chemval,$this, [new Model_Items_Generic_Bone3,new Model_Items_Generic_Waterb]);
                return true;
            default:
                Tool_Scripts::chem_reaction(
                    'Die Chemikalie perlt einfach an dem gefrorenen Körper ab ...',
                    $chemval,$this);
                return false;
        }
	}
}	