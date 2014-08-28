<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Generic_Plasma extends Model_Items_Abstract_Item implements Interface_Static {
	
	protected static $static_info = Array(
			'name' => 'Plasmablitz-Generator',
			'icon' => 'plasma',
			'description' => 'Ein Gerät, dass Luft auf eine ultrahohe Temperatur erhitzen und so Plasma erzeugen kann - und alles was man dafür braucht ist eine kleine Erregerspannung. Damit kannst du doch sicher was tolles bauen - schau am besten mal in deinem lokalen Waffengeschäft vorbei, dort solltest du alle nötigen Werkzeuge finden.',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_RES,
	);

	protected static $weight = 4;
}	