<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Batgun4 extends Model_Combat_Weapons_Ammo implements Interface_Static {

	protected static $static_info = Array(
			'name' => 'Batteriewerfer MK IV Prototyp',
			'icon' => 'batgun4',
			'description' => 'Dieses Gerät wurde kurz nach der Apokalypse vom Militär entwickelt. Die enorm hohe Abschussgeschwindigkeit des MK IV erlaubt maximale Präzision - wenn nötig kannst du damit einem Zombie auf 500m Entfernung den rechten Backenzahn herausschießen (inklusive dem Rest seines Gebisses). Ein hübscher Nebeneffekt dieser Feuerkraft ist die Tatsache, dass die Batterien beim Aufprall zerplatzen und wie Splittergranaten wirken.',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_FIGHT,
	);

	protected static $weight = 10;

	protected static $ammo = ['Model_Items_Battery' => 1];
	protected static $damage = [10,15];
	protected static $range = [1,85];
	protected static $accuracy = 0.9;
	protected static $use_fixed_accuracy = false;
	protected static $aoe = false;

	//public static $reload_time = 1;
}	