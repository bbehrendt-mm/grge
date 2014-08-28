<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Generic_Xmasneedles extends Model_Items_Abstract_Item implements Interface_Static {

    protected static $static_info = Array(
        'name' => 'Beutel mit Tannennadeln',
        'icon' => 'xmasneedles',
        'description' => 'Welch ein merkwürdiger Gegenstand... man könnte meinen, er wäre wegen irgend einer Art von Event in dieses Spiel aufgenommen worden.',
        'category' => Model_Items_Abstract_Item::MIAI_CAT_RES,
    );

	protected static $weight = 3;
}	