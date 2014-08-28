<?php defined('SYSPATH') OR die('No direct access allowed.');

abstract class Model_Buffs_Abstract_Job extends Model_Buffs_Abstract_Buff {
	
	protected static $name = Array('Lv1', 'Lv2', 'Lv3', 'Lv4', 'Lv5');
	protected static $desc = Array('Lv1', 'Lv2', 'Lv3', 'Lv4', 'Lv5');
	
	protected $level = 1;
	
	public function __construct($player_id = NULL, $level) {
		global $game, $player;
		$this->level = $level;
		parent::__construct($player_id, -1);
		
		$this->adjust();
	}
	
	abstract protected function adjust();
	
	public function icon() {
		return "/application/assets/icons/buffs/" . static::$bid . "/" . $this->level . ".gif";
	}
	
	public function name() {
		if (isset(static::$name[$this->level - 1]))
            return static::$name[$this->level - 1];
        else return static::$name[count(static::$name) - 1];
	}
	
	public function description() {
        if (isset(static::$desc[$this->level - 1]))
            return static::$desc[$this->level - 1];
        else return static::$desc[count(static::$name) - 1];
	}
	
}
