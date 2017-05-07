<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Waterbomb extends Model_Combat_Weapons_Throwable implements Interface_Fillable, Interface_Static {
	
	protected static $static_info = Array(
			'name' => 'Wasserbombe',
			'icon' => 'waterbomb',
			'description' => 'Sobald die Zombies in Wurfreichweite kommen kannst du ihnen mit diesem kleinen Geschenk die Tour vermiesen. Nichts ist effektiver gegen eine Gruppe Zombies als eine Wasserbombe!',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_FIGHT,
	);
	
	protected static $weight = 10;

	protected static $damage = [5,50];
	protected static $range = [1,18];
	protected static $accuracy = 0.8;
	protected static $use_fixed_accuracy = true;
	protected static $aoe = true;
	protected static $friendly_fire = false;
	protected static $energy = 1;
	
	public function interaction_fillfrom($id) {
        Globals::PrimaryPlayer()->log()->add('Diese Wasserbombe ist bereits gefüllt.');
		return false;
	}
	
	public function interaction_fill($liquid_id) {
        Globals::PrimaryPlayer()->log()->add('Diese Wasserbombe ist bereits gefüllt.');
		return false;
	}
	
	public function capacity() {
		return 1;
	}
	
	public function fillrate() {
		return 1;
	}
}	