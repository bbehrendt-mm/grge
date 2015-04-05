<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Generic_Lametta extends Model_Items_Abstract_Item implements Interface_Static {

    protected static $static_info = Array(
        'name' => 'Lametta',
        'icon' => 'lametta',
        'description' => 'An Lametta scheiden sich die Geister - die einen lieben es, die anderen hassen es. Der Entwickler dieses Spiels gehört offensichtlich zur ersten Gruppe.',
        'category' => Model_Items_Abstract_Item::MIAI_CAT_RES,
        'deco' => 5,
    );

	protected static $weight = 5;
}	