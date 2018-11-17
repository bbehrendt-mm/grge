<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Buffs_Exited extends Model_Buffs_Abstract_Buff {
	
	protected static $name = 'Aufgeregt';
	protected static $icon = 'wow';
	protected static $desc = 'Du bist ganz schön aufgeregt... das wird dich eine ganze Weile am Schlafen hindern.';
	protected static $bid = 'wow';

    protected function get_effects(): array { return [
        Model_Status::MS_STAT_SLEEPY => Array(
            Model_Buffs_Abstract_Buff::MB_RAISE_ACC => 0,
            Model_Buffs_Abstract_Buff::MB_DROP_ACC => 0,
            Model_Buffs_Abstract_Buff::MB_RAISE_PRC => 0,
            Model_Buffs_Abstract_Buff::MB_DROP_PRC => -1,
        )
    ]; }

    /**
     * Gets called when a new buff with the same buff identifier is cast on a player; the function is called on the resident buff, with the new one as argument
     * @param Model_Buffs_Exited $newclass
     */
    public function merge($newclass) {
        $this->lifetime += $newclass->lifetime;
    }
}
