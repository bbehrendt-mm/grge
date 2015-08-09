<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Batc extends Model_Combat_Weapons_Close implements Interface_Static {

	protected static $static_info = Array(
			'name' => 'Chemische Keule',
			'icon' => 'batc',
			'description' => 'Dieser Schläger ist mit merkwürdigen Substanzen getränkt und wirkt äußerst schädlich auf Zombies, die in näheren Kontakt mit ihm kommen. Allerdings hat er durch die Chemikalien einiges an Stabilität verloren...',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_FIGHT,
	);

	protected static $weight = 6;

	protected static $damage = [15,17];
	protected static $energy = 3;
	protected static $max_range = 1;

	protected static $durabillity = 0.4;
	//public static $reload_time = 1;
}	