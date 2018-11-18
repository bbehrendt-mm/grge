<?php defined('SYSPATH') OR die('No direct access allowed.');

abstract class Model_Combat_Weapons_Energy extends Model_Combat_Weapon {

    protected static $energy = 1;
    protected static $usable_by_child = false;

    public function energy(): int {
        return static::$energy;
    }

    public function usable(): bool {
        return parent::usable() && $this->registered_user && $this->registered_user->get_status()->has(Model_Status::MS_STAT_ENERGY, $this->energy(), Model_Status::MS_EFFECT_REQUIREMENT);
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
        if ($this->registered_user)
            $this->registered_user->get_status()->modify(Model_Status::MS_STAT_ENERGY, -$this->energy(), Model_Status::MS_EFFECT_REQUIREMENT);
        return parent::trigger_usage($me, $opponent, $damage, $scene);
    }

    public function get_ammo_icons(): array {
        return array_merge(parent::get_ammo_icons(), [['::energy', $this->energy()]]);
    }

    public function equip($p = null): void
    {
        /** @global Model_Player $player */
        if ($p === null)
            $player = Globals::PrimaryPlayerF();
        else $player = $p;

        if (!Tool_Scripts::is_npc($player) && $player->job(1080)) {
            $player->log()->add('Als Kind kannst du diese Waffe nicht ausrüsten!');
            return;
        }


        parent::equip($p);
    }

}