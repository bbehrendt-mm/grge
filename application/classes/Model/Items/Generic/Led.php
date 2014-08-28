<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Generic_Led extends Model_Items_Abstract_Item implements Interface_Static {

    protected static $static_info = Array(
        'name' => 'LED',
        'icon' => 'led',
        'description' => 'Diese LED ist so unglaublich energieeffizient, dass sie fast von alleine leuchtet. Allerdings wird dir eine einzige nicht allzu viel bringen, denn sonderlich viel Licht erzeugt sie nicht...',
        'category' => Model_Items_Abstract_Item::MIAI_CAT_RES,
    );

	protected static $weight = 3;
}	