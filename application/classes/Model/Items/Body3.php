<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Body3 extends Model_Items_Abstract_Item implements Interface_Static {

	protected static $static_info = Array(
			'name' => 'Tierkadaver',
			'icon' => 'body3',
			'description' => 'Tja, Zombies ist es wohl ziemlich egal ob sie Jagd auf Menschen oder Tiere machen. Dieses Vieh war mal Zombiesfutter.... jetzt könnte es Futter für dich werden, sofern du ziemlich anspruchslos bist.',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_FOOD,
	);
	
	protected static $weight = 30;


    protected function hid() {
        return parent::hid()
            ->add_action('Fressen', Model_Action::factory()
                    ->effect(
                        Model_Effect::factory()
                            ->effect(Model_Player::MP_STAT_HUNGER, 50)
                            ->effect(Model_Player::MP_STAT_HEALTH, -30)
                            ->consume($this)
                            ->achieve(Model_Achievement::MA_BODY_EATER)
                            ->spawn('Model_Items_Bone')
                            ->message('Nachdem du das runtergeschlungen hast dreht sich dir der Magen um - aber wenigstens ist er wieder voll. Hoffentlich ist deine Hausapotheke das auch ...')
                    )
            );
    }
	
	public function mixchem($chemval) {
        /**
         * @global $player Model_Player
         */
		global $player;

		if ($chemval == 1) {
			$player->log()->add(new Model_Log_Types_Text(null, null, 'Die Chemikalie ätzt der Leiche das Fleisch von den Knochen! Es bleibt ledigtlich etwas Blut zurück... und ein perfekt erhaltenes Skelett!'));
			$player->location()->inventory()->add(new Model_Items_Bone);
			$player->location()->inventory()->add(new Model_Items_Generic_Waterb());
			$this->consume();
			return true;
		} else {
			$player->log()->add(new Model_Log_Types_Text(null, null, 'Der zerfetzte Körper der Leiche saugt die Chemikalie auf, aber du kannst keine Veränderung feststellen ...'));
			return false;
		}
	}
}	