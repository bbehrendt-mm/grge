<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Paracetoid extends Model_Items_Abstract_Pillbox {
	
	protected static $static_info = Array(
			'name' => 'Schachtel mit Paracetoid',
			'icon' => 'paracetoid',
			'description' => 'Hilft zuverlässig gegen Kopfschmerzen, Bauchschmerzen, abgerissene Körperteile und extreme radioaktive Verstrahlung. Nicht einnehmen bei Schwangerschaft oder fortgeschrittener Zombiefizierung.',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_DRUG,
	);

    protected static $autospawn = Array(1,10);
    protected static $take_msg = 'Direkt nachdem du die Paracetoid schluckst merkst du, wie es dir besser geht.';
    protected static $singular_name = 'Paracetoid';
    protected static $pill_effects = Array(
        Model_Player::MP_STAT_HEALTH => 5
    );
	
	public function mixchem($chemval) {
        /**
         * @global $player Model_Player
         */
		global $player;

		if ($chemval == 1) {
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