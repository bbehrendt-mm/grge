<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Ammo extends Model_Items_Abstract_Ammo implements Interface_Autotaker {
	
	protected static $static_info = Array(
			'name' => 'Munition',
			'icon' => 'ammo',
			'description' => 'Welch ein glücklicher Fund - Munition! Soetwas findet man sehr selten, manche behaupten sogar soetwas wie "Munition" existiere gar nicht. Wenn du jetzt noch zufällig etwas hast, womit du diese Munition verschießen kannst haben die Zombies keine Chance mehr!',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_MISC,
	);
	
	protected static $weight = 0;	
	protected static $autospawn = Array(5,8);
	protected static $autoappender = Array('Kugel', 'Kugeln');
}	