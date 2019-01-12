<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Buffs_Morphine extends Model_Buffs_Abstract_Buff {
	
	protected static $name = 'Morphium';
	protected static $icon = 'morphine';
	protected static $desc = 'Du stehst unter dem Einfluss von Morphium. Deine Schmerztoleranz ist stark erhöht.';
	protected static $bid = 'morphine';
    
	public function merge(Model_Buffs_Abstract_Buff $newclass): void
    {
        $this->assoc_player->get_status()->set_cause_of_death('Morphium-Überdosis');
        $this->assoc_player->get_status()->retrieveF('heartbeat')->unbuff();
        $this->assoc_player->get_status()->alive(false);
        $this->assoc_player->log()->add('Du spürst, wie deine Muskeln erschlaffen und dein Bewusstsein dich verlässt. Am Ende bleibt dir nichts als Dunkelheit. Herzlichen Glückwunsch, du bist tot.');
	}

    public function __construct($player_id = NULL, $lifetime = -1) {
        parent::__construct($player_id, $lifetime);
        $this->assoc_player->get_status()->scaling_add(Model_Status::MS_STAT_HEALTH, Model_Status::MS_EFFECT_ITEM  , 'morphine', 0.25);
        $this->assoc_player->get_status()->scaling_add(Model_Status::MS_STAT_HEALTH, Model_Status::MS_EFFECT_BATTLE, 'morphine', 0.25);
    }
    
	public function unbuff(): bool
    {
        $this->assoc_player->get_status()->scaling_remove(Model_Status::MS_STAT_HEALTH, Model_Status::MS_EFFECT_ITEM  , 'morphine');
        $this->assoc_player->get_status()->scaling_remove(Model_Status::MS_STAT_HEALTH, Model_Status::MS_EFFECT_BATTLE, 'morphine');
		return parent::unbuff();
	}
}
