<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Log_Types_Battle_Escape extends Model_Log_Message {
	
	private $state;
		
	/**
	 * Creates an entry message for an escape attempt
	 * @param boolean $comb True -> Succesfull; False -> Failed; -1 -> Impossible
	 */
	public function __construct($comb) {
		$this->state = $comb;
	}

	protected function postprocess($data) {
		return [
			'type' => Model_Log_Message::MLM_BATTLE_ESCAPE,
			'v' => $this->state === true ? 1 : ($this->state === false ? 0 : -1)
		];
	}
}