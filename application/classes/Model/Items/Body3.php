<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Body3 extends Model_Items_Abstract_Item implements Interface_Static {

	protected static $static_info = Array(
			'name' => 'Tierkadaver',
			'icon' => 'body3',
			'description' => 'Tja, Zombies ist es wohl ziemlich egal ob sie Jagd auf Menschen oder Tiere machen. Dieses Vieh war mal Zombiesfutter.... jetzt könnte es Futter für dich werden, sofern du ziemlich anspruchslos bist.',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_FOOD,
            'deco' => -40,
	);
	
	protected static $weight = 30;
    protected $peta_achievement = false;

    public function __construct($pet = false) {
        $this->peta_achievement = $pet;
        parent::__construct(null);
    }

    protected function hid() {
        return parent::hid()
            ->add_action('Fressen', Model_Action::factory()
                    ->effect(
                        Model_Effect::factory()
                            ->effect(Model_Status::MS_STAT_HUNGER, 50)
                            ->effect(Model_Status::MS_STAT_HEALTH, -30)
                            ->consume($this)
                            ->achieve(Model_Achievement::MA_BODY_EATER)
                            ->achieve(Model_Achievement::MA_PETA, $this->peta_achievement ? 1 : 0)
                            ->spawn('Model_Items_Bone')
                            ->message('Nachdem du das runtergeschlungen hast dreht sich dir der Magen um - aber wenigstens ist er wieder voll. Hoffentlich ist deine Hausapotheke das auch ...')
                    )
            );
    }
	
	public function mixchem($chemval) {
        switch ($chemval)
        {
            case 1:
                $this->consume();
                Tool_Scripts::chem_reaction(
                    'Die Chemikalie ätzt der Leiche das Fleisch von den Knochen! Es bleibt ledigtlich etwas Blut zurück... und ein perfekt erhaltenes Skelett!',
                    $chemval,$this, [new Model_Items_Generic_Bone3,new Model_Items_Generic_Waterb]);
                return true;
            default:
                Tool_Scripts::chem_reaction(
                    'Der zerfetzte Körper der Leiche saugt die Chemikalie auf, aber du kannst keine Veränderung feststellen ...',
                    $chemval,$this);
                return false;
        }
	}
}	