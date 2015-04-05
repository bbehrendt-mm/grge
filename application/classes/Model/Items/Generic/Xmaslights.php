<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Generic_Xmaslights extends Model_Items_Abstract_Item implements Interface_Static {

    protected static $static_info = Array(
        'name' => 'Christbaumlichter',
        'icon' => 'lights',
        'description' => 'Diese Lichterkette verbreitet extreme Weihnachtsstimmung. Außerdem ist sie vielseitig einsetzbar; du kannst zum Beispiel einen Weihnachtsbaum damit schmücken.',
        'category' => Model_Items_Abstract_Item::MIAI_CAT_RES,
        'deco' => 10,
    );

	protected static $weight = 12;
}	