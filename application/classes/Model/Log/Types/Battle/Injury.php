<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Log_Types_Battle_Injury extends Model_Log_Message {
	
	private $player, $icon, $name;

	public function __construct($player, $icon, $name) {
		$this->player = $player;
		$this->icon = $icon;
		$this->name = $name;
	}

	protected function postprocess($data) {
		return [
			'type' => Model_Log_Message::MLM_BATTLE_INJURY,
			'player' => $this->player,
			'icon' => $this->icon,
			'name' => __($this->name)
		];
	}
}