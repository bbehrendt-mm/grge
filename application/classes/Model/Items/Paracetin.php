<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Paracetin extends Model_Items_Abstract_Pillbox {

	protected static $static_info = Array(
			'name' => 'Schachtel mit Paracetin',
			'icon' => 'paracetin',
			'description' => 'Paracetin macht selbst den schlaffesten Sack wieder munter. Die hochkonzentrierte Mischung aus Koffein und dem von unserer Marketingabteilung neu entwickelten Acclerin - gewonnen aus natürlichem Orangenextrakt - bewirkt ein sofortiges Auffüllen von Energiereserven!',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_DRUG,
	);

	protected static $autospawn = Array(1,10);
    protected static $take_msg = 'Direkt nachdem du die Paracetin schluckst fühlst du, wie deine Kraft zurückkehrt.';
    protected static $singular_name = 'Paracetin';
    protected static $pill_effects = Array(
        Model_Player::MP_STAT_ENERGY => 5
    );
	
	public function mixchem($chemval) {
        /**
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