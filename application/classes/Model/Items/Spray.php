<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Spray extends Model_Items_Abstract_Item implements Interface_Static {

    protected static $static_info = Array(
        'name' => 'Spray',
        'icon' => 'spray/spray0',
        'description' => 'Dieses Spray verbessert nicht nur deinen Körpergeruch um fast 12 Prozent, es verleiht dir außerdem verschiedene zusätzliche Boni.',
        'category' => Model_Items_Abstract_Item::MIAI_CAT_DRUG,
    );

    protected static $instances_info = Array(
        Array(	'name' => 'Kampfspray (M²K)',		'icon' => 'spray/spray1'),
    );

    protected static $weight = 2;

    //TODO Actions
}