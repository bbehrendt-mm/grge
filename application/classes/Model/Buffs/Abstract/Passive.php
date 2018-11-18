<?php defined('SYSPATH') OR die('No direct access allowed.');

abstract class Model_Buffs_Abstract_Passive extends Model_Buffs_Abstract_Buff {
	
	protected $active = false;
	
	public function visible($remoteable = true): bool
    {
		return parent::visible($remoteable) && $this->active;
	}

    public function active(): bool
    {
        return $this->active;
    }
	
	protected abstract function activator(): bool;
	
	public function rebuild(): bool
    {
		$switch = $this->activator();
		$update = (!$this->active && $switch) || ($this->active && !$switch);	
		$this->active = $switch;
		
		if ($update) $this->assoc_player->get_status()->refresh_char();
		
		return parent::rebuild();
	}

    public function effect($stat, $type): float {
        return $this->active ? parent::effect($stat, $type) : 0;
    }
}
