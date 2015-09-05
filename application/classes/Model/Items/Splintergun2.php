<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Splintergun2 extends Model_Combat_Weapons_Ammo implements Interface_Static {

	protected static $static_info = Array(
			'name' => 'Splitterwerfer Mark II',
			'icon' => 'splintergun2',
			'description' => 'Dieser Splitterwerfer mit verbessertem Druckausgleichsregler, feinjustierter Zielautomatik und integriertem Fluxkompensator übertrifft alle Leistungsdaten des Basismodells um Längen. Außerdem erhöht er den Rambo-Faktor des Trägers sofort um 78.92%.',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_FIGHT,
	);
	
	protected static $weight = 8;

	protected static $animation = Model_Combat_Weapon::MCW_ANIMATION_SHOT_SPLINTER;

	protected static $ammo = ['Model_Items_Splinter' => 1];
	protected static $damage = [15,30];
	protected static $range = [1,30];
	protected static $accuracy = 0.9;
	protected static $use_fixed_accuracy = false;
	protected static $aoe = true;

	// INI, ATK, DEF, ACC
	protected static $effects = [-1,0,0,0];
}	