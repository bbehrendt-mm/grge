<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Watergun extends Model_Items_Abstract_Wbgun {
	
	protected static $static_info = Array(
			'name' => 'Aquablaster XS',
			'icon' => 'watergun',
			'description' => 'Zombies hassen Wasser - dieser Fakt verwandelt eine Wasserpistole für Kinder in ein episches Tötungswerkzeug für Zombies. Du musst nur dafür sorgen, dass der Tank immer voll ist...',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_FIGHT,
	);

	protected static $weight = 5;
	protected static $essential = true;

	protected static $damage = [5,20];
	protected static $range = [0,10];
	protected static $accuracy = 1;
	protected static $use_fixed_accuracy = false;
	protected static $aoe = true;
	protected static $friendly_fire = false;

	// TODO: High accuracy
}	