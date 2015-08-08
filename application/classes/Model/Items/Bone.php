<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Bone extends Model_Combat_Weapons_Close implements Interface_Static {

	protected static $static_info = Array(
			'name' => 'Knochen',
			'icon' => 'bone1',
			'description' => 'Häng dir einen Mammut-Mantel um und zieh den Lendenschurz stramm - dieses Accessoire komplettiert deinen stylischen Neandertal-Look. Wenn du Ärger mit einem Zombie hast, knall ihm einfach dieses Ding über die Rübe und zieh ihn dann in deine Höhle.',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_FIGHT,
            'deco' => -1,
	);
	
	protected static $weight = 5;

	protected static $damage = [1,3];
	protected static $energy = 2;
	protected static $max_range = 0.5;

	//public static $reload_time = 0;
}	