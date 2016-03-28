<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Catclaw extends Model_Combat_Weapon {

    protected static $damage = [2,3];
    protected static $range = [0,2];
    protected static $accuracy = 1;
    protected static $use_fixed_accuracy = true;
    protected static $aoe = false;
    protected static $friendly_fire = false;

    protected $neko = false;

    protected static $animation = Model_Combat_Weapon::MCW_ANIMATION_SLASH_MULTI;

    protected static $static_info = Array(
        'name' => 'Krallen',
        'icon' => 'catclaw',
        'description' => '',
        'category' => Model_Items_Abstract_Item::MIAI_CAT_FIGHT,
    );

    protected static $weight = 0;

    public function __construct($allow_neko = false) {
        parent::__construct();
        $this->neko = $allow_neko;
    }

    public function name() {
        return Tool_Gambling::random(0.05) ? 'Neko Punch!' : parent::name();
    }


    public function generate_wound($damage) {
        if ($damage <= 0) return null;

        $injury = mt_rand(0,100);
        if ($injury < $damage * 1)
            return Model_Buffs_Blood::cls();
        elseif ($injury < $damage * 30)
            return Model_Buffs_Bite::cls();
        else return null;
    }
}