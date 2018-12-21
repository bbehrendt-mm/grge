<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Body extends Model_Items_Abstract_Item implements Interface_Static {

	protected static $static_info = Array(
			'name' => 'Zerfetzte Leiche',
			'icon' => 'body',
			'description' => 'Dieses Ding liegt hier schon eine Weile. Die Kleidung ist zerfetzt und der Körper übersäht mit Bisspuren. Nichtsdestotrotz ist da noch einiges an Fleisch übrig geblieben ... die Zombies scheinen nicht an restlose Verwertung zu glauben. Du könntest deine Zähne auch noch dort reinschlagen - wenn du wirklich so verzweifelt bist.',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_FOOD,
            'deco' => -90,
	);
	
	protected static $instances_info = Array(
			Array(	'name' => 'Leiche eines Wanderers'),
			Array(	'name' => 'Lebloser Körper'),
			Array(	'name' => 'Verstorbener Einsiedler'),
			Array(	'name' => 'Zerrissener Körper'),
	);
	
	protected static $weight = 95;
    protected $body_name;
	
	public function __construct($name = null, $desc = null) {
		parent::__construct();
		if ($name) $this->custom_info['name'] = $name;
		if ($desc) $this->custom_info['description'] = $desc;
	}

    protected function hid(): Model_Hid {
        return parent::hid()
            ->add_action('Fressen', Model_Action::factory()
                    ->effect(
                        Model_Effect::factory()
                            ->effect(Model_Status::MS_STAT_HUNGER, 100)
                            ->effect(Model_Status::MS_STAT_HEALTH, -60)
                            ->consume($this)
                            ->achieve(Model_Achievement::MA_BODY_EATER)
                            ->spawn(Model_Items_Generic_Bone3::cls())
                            ->message('Nachdem du das runtergeschlungen hast dreht sich dir der Magen um - aber wenigstens ist er wieder voll. Hoffentlich ist deine Hausapotheke das auch ...')
                    )
            );
    }

    public function give_name($new_name) {
        $this->body_name = $new_name;
    }

    public function label(): ?string
    {
        return $this->body_name;
    }
	
	public function mixchem($chemval): bool
    {
        switch ($chemval)
        {
            case 6:
                $this->consume();
                Tool_Scripts::chem_reaction(
                    'Die Chemikalie ätzt der Leiche das Fleisch von den Knochen! Es bleibt ledigtlich etwas Blut zurück... und ein perfekt erhaltenes Skelett!',
                    $chemval,$this, [new Model_Items_Generic_Bone3,new Model_Items_Generic_Waterb]);
                return true;
            case 9:
                $this->consume();
                Tool_Scripts::chem_reaction(
                    'Die Chemikalie ätzt der Leiche das Fleisch von den Knochen! Es bleibt ledigtlich etwas Blut zurück... und ein perfekt erhaltenes Skelett!',
                    $chemval,$this, [new Model_Items_Generic_Bone3,new Model_Items_Generic_Waterb,new Model_Items_Generic_Waterb]);
                return true;
            default:
                Tool_Scripts::chem_reaction(
                    'Der zerfetzte Körper der Leiche saugt die Chemikalie auf, aber du kannst keine Veränderung feststellen ...',
                    $chemval,$this);
                return false;
        }
	}
}	