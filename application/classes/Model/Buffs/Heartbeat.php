<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Buffs_Heartbeat extends Model_Buffs_Abstract_Buff {
	
	protected static $name = 'Herzschlag';
	protected static $icon = 'heartbeat';
	protected static $desc = 'Eine alte chinesische Weisheit sagt: Wenn dein Herz aufhört zu schlagen bist du tot!';
	protected static $bid = 'heartbeat';
    protected static $remotable = false;

    protected static $immortal = false;
	
	public function rebuild() {
		if (!static::$immortal && $this->assoc_player->get_status()->get(Model_Status::MS_STAT_HEALTH) < 0.5) $this->unbuff();
		
		return parent::rebuild();
	}

    public function remove() {
        $this->assoc_player->kill();
    }
}