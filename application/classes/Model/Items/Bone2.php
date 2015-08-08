<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Bone2 extends Model_Combat_Weapons_Throwable implements Interface_Static {
	
	protected static $static_info = Array(
			'name' => 'Schädel',
			'icon' => 'bone2',
			'description' => 'To be, or not to be... wobei die Frage eher ist: "Warum zur Hölle schleppst du einen Schädel mit dir rum?". Du könntest dich natürlich damit rausreden, dass du das Ding auf Zombies werfen willst...',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_FIGHT,
            'deco' => -1,
	);
	
	protected static $weight = 5;

	protected static $damage = [1,2];
	protected static $range = [1,20];
	protected static $accuracy = 0.3;
	protected static $use_fixed_accuracy = true;
	protected static $aoe = false;
	protected static $friendly_fire = false;
	protected static $energy = 1;

	//public static $reload_time = 1;
}	