<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Buffs_Drug2 extends Model_Buffs_Abstract_Buff {
	
	protected static $name = 'Drogensucht';
	protected static $icon = 'drug2';
	protected static $desc = 'Du bist von spitzen Nadeln und bunten Pillen abhängig... keine schöne Sache. Du kannst entweder deine Sucht weiter befriedigen, oder du versuchst einen Entzug um deine Sucht loszuwerden.';
	protected static $bid = 'drug2';
	
	public function __construct($player_id = NULL) {
		parent::__construct($player_id);
	}
}
