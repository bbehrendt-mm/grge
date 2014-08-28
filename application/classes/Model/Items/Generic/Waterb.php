<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Generic_Waterb extends Model_Items_Abstract_Liquid implements Interface_Static {

	protected static $static_info = Array(
			'name' => 'Blut',
			'icon' => 'water_blood',
			'description' => 'Wäre dies ein Browserspiel mit Vampiren, dann wäre dieses Item sicher wertvoll. Weil Zombies aber viel cooler sind als Vampire, ist dieses Item einfach nur eklig. Bäh!',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_FOOD,
	);
	
	public function __construct() {
		parent::__construct(55);
	}
}	