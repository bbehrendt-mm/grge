<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Generic_Cloth extends Model_Items_Abstract_Item implements Interface_Static {
	
	protected static $static_info = Array(
        'name' => 'Stofffetzen',
        'icon' => 'cloth',
        'description' => 'Dieses große Stück Stoff ist alleine nicht sehr nützlich, aber eventuell kannst du damit etwas anfangen wenn du mehrere hast...?',
        'category' => Model_Items_Abstract_Item::MIAI_CAT_RES,
        'deco' => 1,
	);

	protected static $weight = 3;
}	