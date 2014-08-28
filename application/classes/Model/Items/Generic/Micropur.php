<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Generic_Micropur extends Model_Items_Abstract_Item implements Interface_Static {
	
	protected static $static_info = Array(
			'name' => 'Micropur Reinigungstablette',
			'icon' => 'micropur',
			'description' => 'Deine Wasserflasche hat sich im Laufe der Zeit in eine biologische Kontaminierungszone verwandelt? Kein Problem! Die neue Micropur Extra4, reinigt, desinfiziert, entkalkt und sorgt für frischen Zitronenduft! Darum wird nur Micropur von führenden Wasserflaschen-Herstellern empfohlen.',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_RES,
	);

	protected static $weight = 2;
}	