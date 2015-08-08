<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Macheteband extends Model_Combat_Weapons_Close implements Interface_Static {

	protected static $static_info = Array(
			'name' => 'Verbesserte Machete',
			'icon' => 'machete_band',
			'description' => 'Der weiche Griff dieser Machete macht es bedeutend leichter, sie in einen Zombie zu rammen. So wird die Postapokalypse noch ein bisschen mehr zum Ponyhof.',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_FIGHT,
	);

	protected static $weight = 10;
	protected static $essential = true;

    protected static $damage = [5,12];
    protected static $energy = 2;
    protected static $max_range = 1;

	//public static $reload_time = 1;
}	