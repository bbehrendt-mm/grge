<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Log_Types_Death extends Model implements Interface_Message {
	
	private $cod;	
	private $timecode;

    /**
     * Creates a message that a ruin has been found
     * @param String $cod Death message
     */
	public function __construct($cod) {
		$this->cod = $cod;
		$this->timecode = time();
	}
	
	public function render_title() {
		return null;
	}
	
	public function render_body() {
		return "<b>" . __('Du bist tot!') . "</b><br /><div class='message_body'>" . __('Du hast soeben deinen letzten Atemzug getan... Du bist auf die folgende schreckliche Art von dieser Welt gegangen: ') . "<span class=\"value\">" . __($this->cod ? $this->cod : "Unbekannte Todesart") . "</span></div>";
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