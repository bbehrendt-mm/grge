<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Splintergun extends Model_Combat_Weapons_Ammo implements Interface_Static {

	protected static $static_info = Array(
			'name' => 'Selbstgebauter Splitterwerfer',
			'icon' => 'splintergun',
			'description' => 'Auf den ersten Blick sieht es aus wie ein Batteriewerfer - allerdings ist die Bauweise etwas kompakter. Dieser Splitterwerfer lässt sich mit Splitterkugeln laden. Er funktioniert ähnlich wie ein Batteriewerfer, kann aber bei richtiger Handhabung wesentlich mehr Schaden anrichten. Durch die explosive Wirkung der Splitterkugeln ist er eher für große Zombiemengen geeignet.',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_FIGHT,
	);
	
	protected static $weight = 5;

	protected static $ammo = ['Model_Items_Splinter' => 1];
	protected static $damage = [10,20];
	protected static $range = [1,20];
	protected static $accuracy = 0.8;
	protected static $use_fixed_accuracy = false;
	protected static $aoe = true;

	//public static $reload_time = 3;
}	