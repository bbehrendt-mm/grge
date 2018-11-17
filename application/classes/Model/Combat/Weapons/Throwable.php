<?php defined('SYSPATH') OR die('No direct access allowed.');

abstract class Model_Combat_Weapons_Throwable extends Model_Combat_Weapons_Energy
{
    protected $usable = true;

    protected static $animation = Model_Combat_Weapon::MCW_ANIMATION_THROW;

    public function usable(): bool {
        return $this->usable && parent::usable();
    }

    public function get_ammo_icons(): array {
        return [$this->icon()];
    }

    /**
     * @param Model_Combat_Actor $me
     * @param Model_Combat_Actor $opponent
     * @param number             $damage
     * @param Model_Combat_Scene $scene
     *
     * @return bool
     * @throws Exception
     */
    public function trigger_usage(Model_Combat_Actor $me, Model_Combat_Actor $opponent, $damage, Model_Combat_Scene $scene): bool {
        $this->usable = false;
        return parent::trigger_usage($me, $opponent, $damage, $scene);
    }

    public function unregister(): Model_Combat_Weapon {
        parent::unregister();
        if (!$this->usable)
            $this->grind();
        return $this;
    }
}