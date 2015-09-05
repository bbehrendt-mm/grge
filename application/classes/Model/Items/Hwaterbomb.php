<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Hwaterbomb extends Model_Items_Waterbomb {

	protected static $static_info = Array(
			'name' => 'Weihasserbombe',
			'icon' => 'waterbombh',
			'description' => 'Sobald die Zombies in Wurfreichweite kommen kannst du ihnen mit diesem kleinen Geschenk die Tour vermiesen. Nichts ist effektiver gegen eine Gruppe Zombies als eine Wasserbombe - außer natürlich einer Weihwasserbombe!!',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_FIGHT,
	);

	protected static $damage = [25,60];
	protected static $range = [1,24];
	protected static $accuracy = 0.85;

	public function drop_dead() {
		return new Model_Items_Waterbomb();
	}
}	