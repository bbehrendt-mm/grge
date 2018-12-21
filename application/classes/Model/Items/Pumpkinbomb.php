<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Pumpkinbomb extends Model_Combat_Weapons_Throwable implements Interface_Static {

	protected static $static_info = Array(
			'name' => 'Kürbisbombe',
			'icon' => 'pumpkinbomb',
			'description' => 'Süßes sonst gibts Saures! Diese Kürbisbombe ist äußerst effektiv gegen in der Nähe herumstehende Zombies, die dir einfach keine Bonbons geben wollen.',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_FIGHT,
            'deco' => 10,
	);
	
	protected static $weight = 10;

	protected static $damage = [60,100];
	protected static $range = [0,8];
	protected static $accuracy = 0.85;
	protected static $aoe = true;
	protected static $energy = 10;

	// INI, ATK, DEF, ACC
	protected static $effects = [-1,0,0,0];
}	