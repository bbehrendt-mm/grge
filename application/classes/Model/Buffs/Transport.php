<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Buffs_Transport extends Model_Buffs_Abstract_Passive {
	
	protected static $name = 'Transportmittel';
	protected static $icon = 'transport';
	protected static $desc = 'Du hast ein Transportmittel gefunden! Jetzt kannst du dich wesentlich leichter in der Welt bewegen!';
	protected static $bid = 'transport';

    protected $effects = Array(
				Model_Status::MS_CHAR_DISTANCING => Array(
						Model_Buffs_Abstract_Buff::MB_RAISE_ACC => 0,
						Model_Buffs_Abstract_Buff::MB_DROP_ACC => 0,
						Model_Buffs_Abstract_Buff::MB_RAISE_PRC => 0,
						Model_Buffs_Abstract_Buff::MB_DROP_PRC => 0,
				),				
			);
	
	protected function activator() {
		$i = $this->associated_to_player() ? Tool_Scripts::get_active_transport($this->assoc_player) : null;
        if ($i !== null) {
            $this->effects[Model_Status::MS_CHAR_DISTANCING][Model_Buffs_Abstract_Buff::MB_DROP_ACC] = $i->speedup();
            return true;
        } else return false;
	}
}
