<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Buffs_Passout extends Model_Buffs_Abstract_Buff {
	
	protected static $bid = 'passout';
	protected $count = 1;
	protected static $visible = false;
	
	public function rebuild() {
		if ($this->assoc_player->stats_get(Model_Player::MP_STAT_HEALTH) < 0.5) $this->unbuff();
		
		return parent::rebuild();
	}
	
	public function __construct($player_id = NULL, $lifetime = -1) {
		$this->lifetime = $lifetime;
		
		parent::__construct($player_id);
	}
	
	public function unbuff() {
		$this->count--;
		if ($this->count <= 0) return parent::unbuff();
        return false;
	}
	
	public function merge($newclass) {
		$this->count++;
	}
}
