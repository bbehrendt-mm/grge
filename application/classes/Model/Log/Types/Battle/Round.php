<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Log_Types_Battle_Round extends Model_Log_Message {
	
	private $num;

	public function __construct($num) {
		$this->num = $num;
	}

	protected function postprocess($data) {
		return [
			'type' => Model_Log_Message::MLM_BATTLE_ROUND,
			'round' => $this->num
		];
	}
}