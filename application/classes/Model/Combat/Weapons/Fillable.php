<?php defined('SYSPATH') OR die('No direct access allowed.');

abstract class Model_Combat_Weapons_Fillable extends Model_Combat_Weapon
{
    public static $ammo_icon;
    protected $fillrate = 0;

    public function usable() {
        return $this->fillrate > 0 && parent::usable();
    }

    public function get_ammo_icons() {
        return [static::$ammo_icon];
    }

    /**
     * @param Model_Combat_Actor $me
     * @param Model_Combat_Actor $opponent
     * @param number $damage
     * @param Model_Combat_Scene $scene
     * @return bool
     */
    public function trigger_usage($me, $opponent, $damage, $scene) {
        $this->fillrate--;
        return parent::trigger_usage($me, $opponent, $damage, $scene);
    }
}