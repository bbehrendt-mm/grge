<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Snowball extends Model_Combat_Weapons_Throwable implements Interface_Static {
	
	protected static $static_info = Array(
			'name' => 'Schneeball',
			'icon' => 'snowball',
			'description' => 'Eine Schneeballschlacht ist immer lustig - insbesondere wenn deine Gegenspieler beim Kontakt mit Wasser schmelzen!',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_FIGHT,
	);
	
	protected static $weight = 0.2;

	protected static $damage = [5,12];
	protected static $range = [1,25];
	protected static $accuracy = 0.9;
	protected static $aoe = true;
}	