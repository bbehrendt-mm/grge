<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Battle_Saint extends Model_Battle_Player {
	
	protected static $unarmed = 'Model_Battle_Unarmed_Holy';
	
	protected function sync() {
		return;
	}
	
	public function __construct($pid) {
		global $game;
		
		parent::__construct($pid);
		$this->name = "Gott";
		$this->inventory = new Model_Inventory();
		
		$this->health = 100;
		$this->energy = 100;
	}
	
	public function damage($dmg) {
		return 0;
	}
}