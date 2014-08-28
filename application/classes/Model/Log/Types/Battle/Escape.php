<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Log_Types_Battle_Escape extends Model implements Interface_Message {
	
	private $type;
		
	/**
	 * Creates an entry message for an escape attempt
	 * @param boolean $comb True -> Succesfull; False -> Failed; -1 -> Impossible
	 */
	public function __construct($type) {
		$this->type = $type;
	}
	
	public function render_title() {
		return NULL;
	}
	
	public function render_body() {
		if ($this->type === -1) return "<img alt=\"?\" src=\"/application/assets/icons/arrowr_r.gif\"></img> " . __('Es gibt kein Entkommen!');
		elseif ($this->type) return "<img alt=\"?\" src=\"/application/assets/icons/arrow_r.gif\"></img> " . __('Gerade noch so entkommen! Das war knapp...');
		else return "<img alt=\"?\" src=\"/application/assets/icons/arrowr_r.gif\"></img> " . __('Eine Flucht scheint aussichtslos...');
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