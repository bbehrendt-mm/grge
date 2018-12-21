<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Batgunsnp extends Model_Combat_Weapons_Ammo implements Interface_Static {

	protected static $static_info = Array(
			'name' => 'Scharfschützen-Batteriewerfer',
			'icon' => 'batgun_snp',
			'description' => 'Wenn du nicht so darauf stehst, wie Rambo wild in der Gegend rumzuballern und trotzdem nichts zu treffen, dann benutze dieses hochelegante Batterie-Scharfschützengewehr. Jeder Schuss ist äußerst tödlich und garantiert ein Treffer - vorrausgesetzt, du hast dir die Zeit zum Zielen genommen.',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_FIGHT,
	);

	protected static $weight = 12;

	protected static $ammo = ['Model_Items_Battery' => 1];
	protected static $damage = [50,100];
	protected static $range = [20,100];

	protected static $animation = Model_Combat_Weapon::MCW_ANIMATION_SHOT_BAT;

	// INI, ATK, DEF, ACC
	protected static $effects = [-5,0,0,0];
}	