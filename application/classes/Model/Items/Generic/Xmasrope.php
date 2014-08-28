<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Generic_Xmasrope extends Model_Items_Abstract_Item implements Interface_Static {

    protected static $static_info = Array(
        'name' => 'Schmuckseil',
        'icon' => 'xmasrope',
        'description' => 'Eine Last kann dieses Seil nicht tragen - einen Weihnachtsbaum schmücken hingegen schon.',
        'category' => Model_Items_Abstract_Item::MIAI_CAT_RES,
    );

	protected static $weight = 12;
}	