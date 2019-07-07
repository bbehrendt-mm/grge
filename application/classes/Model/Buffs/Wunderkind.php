<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Buffs_Wunderkind extends Model_Buffs_Abstract_Passive {
	
	protected static $name = 'Benebelt';
	protected static $icon = 'tumble_drunk';
	protected static $desc = 'Dein genialer Verstand ist vernebelt; möglicherweise werden einige deiner Heldentaten in diesem Zustand nicht so funktionieren, wie sie sollten ...';
	protected static $bid = 'wdrunk';
	
	protected function activator(): bool {
        return $this->assoc_player->get_status()->get(Model_Status::MS_STAT_DRUNK) > 15;
	}
}
