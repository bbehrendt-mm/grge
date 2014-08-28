<?php defined('SYSPATH') OR die('No direct access allowed.');

abstract class Model_Buffs_Abstract_Passive extends Model_Buffs_Abstract_Buff {
	
	protected $active = false;
	
	public function visible() {
		return $this->active;
	}

    public function active() {
        return $this->active;
    }
	
	protected abstract function activator();
	
	public function rebuild() {
		$switch = $this->activator();
		$update = (!$this->active && $switch) || ($this->active && !$switch);	
		$this->active = $switch;
		
		if ($update) $this->assoc_player->refresh_char_values();
		
		return parent::rebuild();
	}

    public function effect($stat, $type) {
        return $this->active ? parent::effect($stat, $type) : 0;
    }
}
