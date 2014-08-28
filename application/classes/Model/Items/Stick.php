<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Stick extends Model_Battle_Weapon implements Interface_Static {
    protected static $static_info = Array(
        'name' => 'Brüchiger Stock',
        'icon' => 'stick',
        'description' => 'Ein brüchiger Stock ist auf sehr viele verschiedene Arten nutzlos; du kannst damit nichts bauen, und wenn du damit auf Zombies losgehst wirst du dir sicher ein paar Bissabdrücke einfangen.',
        'category' => Model_Items_Abstract_Item::MIAI_CAT_GEAR,
    );

	protected static $weight = 3;

	public static $range = Array(0,1);
	protected static $damage = Array(0,1);
	public static $damage_type = Model_Battle_Weapon::MBW_DMG_IMPACT;
	protected static $ammo = null;
	public static $accuracy = 1;
	public static $accuracy_type = Model_Battle_Weapon::MBW_ACC_STATIC;
	public static $durability = 0.2;
	public static $bounce = 1;
	public static $reload_time = 0;
	public static $lock_type = Model_Battle_Weapon::MBW_LCK_PLAYER;
	public static $energy_cost = 1;
}	