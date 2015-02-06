<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Log_Types_Battle_Death extends Model_Log_Message {
	
	private $name;
	private $is_zombie;
    private $special;
	
	/**
	 * Creates a death message for a combatant
	 * @param Model_Battle_Combatant $comb
	 */
	public function __construct($comb) {
		$this->is_zombie = $comb->is_zombie();
		$this->name = $comb->name();
        $this->special = $comb->special();
	}

	protected function postprocess($data) {
		return [
			'type' => Model_Log_Message::MLM_BATTLE_DEATH,
			'zombie' => $this->is_zombie,
			'name' => $this->is_zombie ? __($this->name) : $this->name,
			'ren' => ($this->is_zombie && $this->special),
		];
	}
}