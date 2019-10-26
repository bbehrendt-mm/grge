<?php defined('SYSPATH') OR die('No direct access allowed.');

abstract class Model_Combat_Weapons_Ammo extends Model_Combat_Weapon
{
    protected static $ammo = [];

    protected $build_in_ammo = 0;

    public function ammo(): array {
        return static::$ammo;
    }

    public function set_built_in_ammo(int $set) {
        $this->build_in_ammo = $set;
    }

    public function usable(): bool {
        if ($this->usable_without_player) return ($this->build_in_ammo > 0);
        foreach ($this->ammo() as $type => $count)
            if (Tool_Scripts::count_items($type, Struct_ScriptItemSource::onlyPlayer()->use_perspective($this->registered_user)) < $count)
                return false;
        return parent::usable();
    }

    public function get_ammo_icons(): array
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
     * @param number             $damage
     * @param Model_Combat_Scene $scene
     *
     * @return bool
     * @throws Exception
     */
    public function trigger_usage(Model_Combat_Actor $me, Model_Combat_Actor $opponent, $damage, Model_Combat_Scene $scene): bool {
        if ($this->usable_without_player && $this->build_in_ammo > 0)
            $this->build_in_ammo--;
        elseif ($this->registered_user)
            Tool_Scripts::consume_items(Struct_ItemEntry::convert($this->ammo()), Struct_ScriptItemSource::onlyPlayer()->use_perspective($this->registered_user));

        return parent::trigger_usage($me, $opponent, $damage, $scene);
    }
}