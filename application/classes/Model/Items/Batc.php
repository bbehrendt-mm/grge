<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Batc extends Model_Battle_Weapon implements Interface_Static {

	protected static $static_info = Array(
			'name' => 'Chemische Keule',
			'icon' => 'batc',
			'description' => 'Dieser Schläger ist mit merkwürdigen Substanzen getränkt und wirkt äußerst schädlich auf Zombies, die in näheren Kontakt mit ihm kommen. Allerdings hat er durch die Chemikalien einiges an Stabilität verloren...',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_FIGHT,
	);

	protected static $weight = 6;

	public static $range = Array(0,1);
	protected static $damage = Array(15,17);
	public static $damage_type = Model_Battle_Weapon::MBW_DMG_IMPACT;
	protected static $ammo = null;
	public static $accuracy = 1;
	public static $accuracy_type = Model_Battle_Weapon::MBW_ACC_STATIC;
	public static $durability = 0.4;
	public static $bounce = 1;
	public static $reload_time = 1;
	public static $lock_type = Model_Battle_Weapon::MBW_LCK_PLAYER;
	public static $energy_cost = 3;

}	