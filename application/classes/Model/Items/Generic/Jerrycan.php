<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Generic_Jerrycan extends Model_Items_Abstract_Item implements Interface_Static {
	
	protected static $static_info = Array(
			'name' => 'Kraftstoff-Kanister',
			'icon' => 'jerrycan',
			'description' => 'Kraftstoff ist selten geworden nach der Zombieinvasion... hauptsächlich, weil die OPEC jede Gelegenheit nutzt, die Fördermengen zu senken. Freu dich also, dass du zumindest diesen einen, halbvollen Kanister gefunden hast!',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_RES,
	);

	protected static $weight = 10;
}	