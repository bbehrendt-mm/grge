<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Generic_Clothes extends Model_Items_Abstract_Item implements Interface_Static {

    protected static $static_info = Array(
        'name' => 'Zerrissene Klamotten',
        'icon' => 'clothes2',
        'description' => 'Dieser Haufen Stoff war sicher mal bequeme Kleidung - jetzt kannst du damit höchstens noch dein Versteck auswischen.',
        'category' => Model_Items_Abstract_Item::MIAI_CAT_MISC,
    );

    protected static $weight = 0;

}	