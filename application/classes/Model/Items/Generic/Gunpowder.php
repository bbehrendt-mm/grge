<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Generic_Gunpowder extends Model_Items_Abstract_Item implements Interface_Static {
	
	protected static $static_info = Array(
			'name' => 'Schwarzpulver',
			'icon' => 'gunpowder',
			'description' => 'Schon die alten Chinesen wussten: Schwarzpulver ist vielseitig einsetzbar. Zum Beispiel kann man es bei Bedarf explodieren lassen und so alle möglichen coolen Effekte erzeugen!',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_RES,
	);

	protected static $weight = 1;
}	