<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Generic_Wire extends Model_Items_Abstract_Item implements Interface_Static {

    protected static $static_info = Array(
        'name' => 'Rolle Draht',
        'icon' => 'wire',
        'description' => 'Draht kann man eigentlich immer gebrauchen. Er ist zum beispiel nützlich um irgendwelchen Elektromüll zu verkabeln. Oder du kannst damit lustige Fallen in deinem Versteck bauen, um mit den Zombies so eine "Kevin allein zu Haus"-Nummer abzuziehen.',
        'category' => Model_Items_Abstract_Item::MIAI_CAT_RES,
    );

	protected static $weight = 5;
}	