<?php defined('SYSPATH') OR die('No direct access allowed.');

abstract class Model_Combat_Weapons_Ammo extends Model_Combat_Weapon
{
    protected static $ammo = [];

    public function ammo() {
        return static::$ammo;
    }

    public function usable() {
        if (!$this->registered_user) return false;
        foreach ($this->ammo() as $type => $count)
            if (Tool_Scripts::count_available_items($type, true, false, false, $this->registered_user) < $count)
                return false;
        return parent::usable();
    }

    public function get_ammo_icons()
    {
        $tmp = [];
        foreach ($this->ammo() as $type => $count)
            /** @var Model_Items_Abstract_Item $type */
            for ($i = 0; $i < $count; $i++)
                $tmp[] = $type::static_icon();
        return $tmp;
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
            Tool_Scripts::consume_available_items($this->ammo(), true, false, false, $this->registered_user);
        return parent::trigger_usage($me, $opponent, $damage, $scene);
    }
}