<?php defined('SYSPATH') OR die('No direct access allowed.');

abstract class Model_Combat_Weapons_Energy extends Model_Combat_Weapon {

    protected static $energy = 1;

    public function energy() {
        return static::$energy;
    }

    public function usable() {
        return parent::usable() && $this->registered_user && $this->registered_user->get_status()->has(Model_Status::MS_STAT_ENERGY, $this->energy(), Model_Status::MS_EFFECT_REQUIREMENT);
    }

    /**
     * @param Model_Combat_Actor $me
     * @param Model_Combat_Actor $opponent
     * @param number $damage
     * @param Model_Combat_Scene $scene
     * @return bool
     */
    public function trigger_usage(Model_Combat_Actor $me, Model_Combat_Actor $opponent, $damage, Model_Combat_Scene $scene) {
        if ($this->registered_user)
            $this->registered_user->get_status()->modify(Model_Status::MS_STAT_ENERGY, -$this->energy(), Model_Status::MS_EFFECT_REQUIREMENT);
        return parent::trigger_usage($me, $opponent, $damage, $scene);
    }

}