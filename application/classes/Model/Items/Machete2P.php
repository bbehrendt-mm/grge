<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Machete2P extends Model_Combat_Weapons_Close implements Interface_Static {

    protected static $static_info = Array(
        'name' => 'Presitentiale Machete',
        'icon' => 'machete2p',
        'description' => 'Gehört zur Standard-Ausrüstung jedes Präsidenten.',
        'category' => Model_Items_Abstract_Item::MIAI_CAT_FIGHT,
    );

    protected static $weight = 9;
    protected static $essential = true;

    protected static $damage = [5,15];
    protected static $energy = 2;

    protected static $animation = Model_Combat_Weapon::MCW_ANIMATION_SLASH;
}	