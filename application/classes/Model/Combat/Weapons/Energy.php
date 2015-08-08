<?php defined('SYSPATH') OR die('No direct access allowed.');

abstract class Model_Combat_Weapons_Energy extends Model_Combat_Weapon {

    protected static $energy = 1;

    public function energy() {
        return static::$energy;
    }

    public function usable() {
        return parent::usable() && $this->registered_user && $this->registered_user->stats_get(Model_Player::MP_STAT_ENERGY) >= $this->energy();
    }

    /**
     * @param Model_Combat_Actor $me
     * @param Model_Combat_Actor $opponent
     * @param number $damage
     * @param Model_Combat_Scene $scene
     * @return bool
     */
    public function trigger_usage($me, $opponent, $damage, $scene) {
        if ($this->registered_user)
            $this->registered_user->stats_modify([Model_Player::MP_STAT_ENERGY, -$this->energy()]);
        return parent::trigger_usage($me, $opponent, $damage, $scene);
    }

}