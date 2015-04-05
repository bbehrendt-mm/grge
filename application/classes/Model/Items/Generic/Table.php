<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Generic_Table extends Model_Items_Abstract_Item implements Interface_Static {
	
	protected static $static_info = Array(
        'name' => 'Järpen-Tisch',
        'icon' => 'table',
        'description' => 'Dieser mit nur leichten Gebrauchsspuren versehene Järpen-Tisch verwandelt selbst die schäbigste Bruchbude in... naja, eine schäbige Bruchbude mit Tisch.',
        'category' => Model_Items_Abstract_Item::MIAI_CAT_RES,
        'deco' => 2,
	);

	protected static $weight = 60;
}	