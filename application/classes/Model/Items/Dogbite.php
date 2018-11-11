<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Dogbite extends Model_Combat_Weapon {

    protected static $damage = [1,3];
    protected static $range = [0,1];
    protected static $accuracy = 0.8;
    protected static $use_fixed_accuracy = true;
    protected static $aoe = false;
    protected static $friendly_fire = false;

    protected static $animation = Model_Combat_Weapon::MCW_ANIMATION_ZOMBIE_MUNCH;

    protected static $static_info = Array(
        'name' => 'Kräftiger Kiefer',
        'icon' => 'dogbite',
        'description' => '',
        'category' => Model_Items_Abstract_Item::MIAI_CAT_FIGHT,
    );

    protected static $weight = 0;

    public function generate_wound($damage) {
        if ($damage <= 0) return null;

        $injury = random_int(0,100);
        if ($injury < $damage * 2)
            return Model_Buffs_Blood::cls();
        elseif ($injury < $damage * 20)
            return Model_Buffs_Bite::cls();
        else return null;
    }
}