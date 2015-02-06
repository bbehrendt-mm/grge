<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Log_Types_Battle_Enter extends Model_Log_Message {
	
	private $name;
	private $count;
	private $distance;
	private $is_zombie;
    private $special;

	/**
	 * Creates an entry message for a combatant
	 * @param Model_Battle_Combatant $comb
	 */
	public function __construct($comb) {
		$this->is_zombie = $comb->is_zombie();
		$this->name = $comb->name();
		$this->count = $comb->count();
		$this->distance = $comb->distance();
        $this->special = $comb->special();
	}

	protected function postprocess($data) {
		return [
			'type' => Model_Log_Message::MLM_BATTLE_ENTER,
			'zombie' => $this->is_zombie,
			'name' => $this->is_zombie ? __($this->name) : $this->name,
			'ren' => ($this->is_zombie && $this->special),
			'distance' => $this->is_zombie ? $this->distance : 0,
			'count' => $this->count
		];
	}
}