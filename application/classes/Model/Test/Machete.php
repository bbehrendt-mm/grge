<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Test_Machete extends Model_Combat_Weapon {

    protected static $damage = [5,8];
    protected static $range = [0,1];
    protected static $accuracy = 1;
    protected static $use_fixed_accuracy = true;
    protected static $aoe = false;
    protected static $friendly_fire = false;

    protected static $static_info = Array(
        'name' => 'Rostige Machete',
        'icon' => 'machete',
        'description' => 'Obwohl leicht abgestumpft und rostig ist diese Machete immer noch effektiv im Kampf gegen Zombiehorden. Noch besser ist es natürlich, die Zombies gar nicht erst in Machetenreichweite kommen zu lassen.',
        'category' => Model_Items_Abstract_Item::MIAI_CAT_FIGHT,
    );

    protected static $weight = 10;
    protected static $essential = true;

}