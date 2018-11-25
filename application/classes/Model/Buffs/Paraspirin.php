<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Buffs_Paraspirin extends Model_Buffs_Abstract_Buff {
	
	protected static $name = 'Paraspirin';
	protected static $icon = 'paraspirine';
	protected static $desc = 'Du hast Paraspirin geschluckt, und bist damit endlich deine quälenden Kopfschmerzen los. Allerdings solltest du von Alkohol jetzt erst einmal etwas Abstand nehmen...';
	protected static $bid = 'paraspirine';
    
	public function merge($newclass): void
    {
        $this->lifetime += $newclass->lifetime();
        Tool_Numerics::bounds($this->lifetime, 0, 144);
	}

    public function __construct($player_id = NULL, $lifetime = -1) {
        parent::__construct($player_id, $lifetime);
        $this->assoc_player->get_status()->scaling_add(Model_Status::MS_STAT_DRUNK, Model_Status::MS_EFFECT_ITEM, 'paraspirine', 8);
        $this->assoc_player->get_status()->scaling_add(Model_Status::MS_STAT_DRUNK, Model_Status::MS_EFFECT_BUFF, 'paraspirine', 0);
    }
    
	public function unbuff(): bool {
        $this->assoc_player->get_status()->scaling_remove(Model_Status::MS_STAT_DRUNK, Model_Status::MS_EFFECT_ITEM, 'paraspirine');
        $this->assoc_player->get_status()->scaling_remove(Model_Status::MS_STAT_DRUNK, Model_Status::MS_EFFECT_BUFF, 'paraspirine');
		return parent::unbuff();
	}
}
