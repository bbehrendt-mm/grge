<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Log_Types_Building extends Model implements Interface_Message {

    private $uin;
	private $name;	
	private $timecode;
	private $rcode;
	
	/**
	 * Creates a message that a ruin has been found
	 * @param Model_Places_Abstract_Place $ruin Short message title
	 */
	public function __construct($ruin) {
		global $player;
        $this->uin = $player->user_id();

        $this->name = $ruin->name();
		$this->rcode = mt_rand(0, 2);
		
		$this->timecode = time();
	}
	
	public function render_title() {
		return __(':building aufgedeckt!', array(':building' => '<span class="value">' . __($this->name) . '</span>'));
	}
	
	public function render_body() {
		global $player, $game;
        $insert = '';

        if ($player->user_id() == $this->uin) {
            switch ($this->rcode) {
                case 0: $insert = __("Die Sterne waren dir wohlgesonnen! Nachdem du ewig durch die Ödniss geirrt bist hast du nun endlich folgende Ruine aufgedeckt: "); break;
                case 1: $insert = __("Obwohl du dir sicher bist, hier schon einmal gesucht zu haben, entdeckst du folgende Ruine: "); break;
                case 2: $insert = __("Tja, hier hättest du mal eher suchen sollen - hat ja ganz schön gedauert, bis du diese Ruine endlich gefunden hast: "); break;
            }

            return "<b>" . __('Du hast eine Ruine aufgedeckt!') . "</b><br /><div class='message_body'>{$insert}<span class=\"value\">" . __($this->name) . "</span></div>";
        } else {
            switch ($this->rcode) {
                case 0: $insert = __("Die Sterne waren :name anscheinend wohlgesonnener als dir! Nachdem er ewig durch die Ödniss geirrt ist hast er endlich folgende Ruine aufgedeckt: ", array(':name' => $game->get_player($this->uin)->name())); break;
                case 1: $insert = __("Obwohl du dir sicher bist, hier schon einmal gesucht zu haben, entdeckt :name folgende Ruine: ", array(':name' => $game->get_player($this->uin)->name())); break;
                case 2: $insert = __("Tja, hier hättest du mal eher suchen sollen - jetzt hat :name an deiner Stelle folgende Ruine gefunden: ", array(':name' => $game->get_player($this->uin)->name())); break;
            }

            return "<b>" . __(':name hat eine Ruine aufgedeckt!', array(':name' => $game->get_player($this->uin)->name())) . "</b><br /><div class='message_body'>{$insert}<span class=\"value\">" . __($this->name) . "</span></div>";
        }
	}
	
	public function timecode() {
		return $this->timecode;
	}

    /**
     * @param Interface_Message $new
     * @return bool
     */
    public function merge($new) {
        return false;
    }
}