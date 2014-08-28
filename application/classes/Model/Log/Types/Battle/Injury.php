<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Log_Types_Battle_Injury extends Model implements Interface_Message {
	
	private $player, $icon, $name;
	
		
	/**
	 * Creates an entry message for an escape attempt
	 * @param boolean $comb True -> Succesfull; False -> Failed; -1 -> Impossible
	 */
	public function __construct($player, $icon, $name) {
		$this->player = $player;
		$this->icon = $icon;
		$this->name = $name;
	}
	
	public function get_name() {
		return $this->player;
	}
	
	public function get_icon() {
		return $this->icon;
	}
	
	public function render_title() {
		return NULL;
	}
	
	public function render_body() {
		return "<img alt=\"?\" src=\"/application/assets/icons/citizen.gif\"></img> " . __(':name hat eine Verletzung davongetragen: ', array(':name' => "<b>{$this->player}</b>")). "<img src=\"{$this->icon}\" alt=\"?\"> <b>" . __($this->name) . "</b>";
	}
	
	public function timecode() {
		return NULL;
	}

    /**
     * @param Interface_Message $new
     * @return bool
     */
    public function merge($new) {
        return false;
    }
}