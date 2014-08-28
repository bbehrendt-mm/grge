<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Body extends Model_Items_Abstract_Item implements Interface_Static {

	protected static $static_info = Array(
			'name' => 'Zerfetzte Leiche',
			'icon' => 'body',
			'description' => 'Dieses Ding liegt hier schon eine Weile. Die Kleidung ist zerfetzt und der Körper übersäht mit Bisspuren. Nichtsdestotrotz ist da noch einiges an Fleisch übrig geblieben ... die Zombies scheinen nicht an restlose Verwertung zu glauben. Du könntest deine Zähne auch noch dort reinschlagen - wenn du wirklich so verzweifelt bist.',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_FOOD,
	);
	
	protected static $instances_info = Array(
			Array(	'name' => 'Leiche eines Wanderers'),
			Array(	'name' => 'Lebloser Körper'),
			Array(	'name' => 'Verstorbener Einsiedler'),
			Array(	'name' => 'Zerrissener Körper'),
	);
	
	protected static $weight = 95;
	
	public function __construct($name = null, $desc = null) {
		parent::__construct();
		if ($name) $this->custom_info["name"] = $name;
		if ($desc) $this->custom_info["description"] = $desc;	
	}

    protected function hid() {
        return parent::hid()
            ->add_action('Fressen', Model_Action::factory()
                    ->effect(
                        Model_Effect::factory()
                            ->effect(Model_Player::MP_STAT_HUNGER, 100)
                            ->effect(Model_Player::MP_STAT_HEALTH, -60)
                            ->consume($this)
                            ->achieve(Model_Achievement::MA_BODY_EATER)
                            ->spawn('Model_Items_Generic_Bone3')
                            ->message('Nachdem du das runtergeschlungen hast dreht sich dir der Magen um - aber wenigstens ist er wieder voll. Hoffentlich ist deine Hausapotheke das auch ...')
                    )
            );
    }
	
	public function mixchem($chemval) {
        /**
         * @global $player Model_Player
         */
		global $player;

		if ($chemval == 6) {
			$player->log()->add(new Model_Log_Types_Text(null, null, 'Die Chemikalie ätzt der Leiche das Fleisch von den Knochen! Es bleibt ledigtlich etwas Blut zurück... und ein perfekt erhaltenes Skelett!'));
			$player->location()->inventory()->add(new Model_Items_Generic_Bone3);
			$player->location()->inventory()->add(new Model_Items_Generic_Waterb());
			$this->consume();
			return true;
		} else {
			$player->log()->add(new Model_Log_Types_Text(null, null, 'Der zerfetzte Körper der Leiche saugt die Chemikalie auf, aber du kannst keine Veränderung feststellen ...'));
			return false;
		}
	}
}	