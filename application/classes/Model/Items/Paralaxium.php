<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Paralaxium extends Model_Items_Abstract_Pillbox {

	protected static $static_info = Array(
			'name' => 'Schachtel mit Paralaxium',
			'icon' => 'paralaxium',
			'description' => 'Manchmal muss man einfach mal abschalten; Paralaxium hilft dir dabei. Mit nur ein paar Kapseln bist du selbst dann noch entspannt, wenn Zombies an deinem Kopf knabbern.',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_DRUG,
	);

    protected static $autospawn = Array(1,5);
    protected static $take_msg = 'Direkt nachdem du die Paralaxium schluckst fallen dir langsam die Augen zu...';
    protected static $singular_name = 'Paralaxium';
    protected static $pill_effects = Array(
        Model_Player::MP_STAT_SLEEPY => -5
    );
	
	public function mixchem($chemval) {
        /**
         * @global $game Model_Game
         * @global $player Model_Player
         */
		global $player;

		if ($chemval == 6) {
			$player->log()->add(new Model_Log_Types_Text(null, null, 'Die Pillen saugen die Chemikalie regelrecht auf! Wow, du hast Twinoid erzeugt!'));
			$player->location()->inventory()->add(new Model_Items_Twinoid($this->count));
			$this->grind();
			return true;
		} else {
			$player->log()->add(new Model_Log_Types_Text(null, null, 'Die Chemikalie perlt von den Pillen ab... das hat wohl nichts gebracht.'));
			return false;
		}
	}
}	