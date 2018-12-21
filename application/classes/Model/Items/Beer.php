<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Beer extends Model_Items_Abstract_Alcohol implements Interface_Static {
	
	protected static $static_info = Array(
			'name' => 'Bier',
			'icon' => 'beer',
			'description' => '',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_FOOD,
	);

    protected static $instances_info = Array(
        Array(	'name' => 'Weißbier',
            'icon' => 'beer',
            'description' => 'Die gute Nachricht: Es ist Bier. Die schlechte Nachricht: Es ist Otterberger, das einzige Bier das billiger ist als seine Zutaten. Naja, wer wird schon wählerisch sein ...'),
        Array(	'name' => 'Schwarzbier',
            'icon' => 'beer2',
            'description' => 'Die gute Nachricht: Es ist Bier. Die schlechte Nachricht: Du kannst nicht sicher sagen, ob es echtes Schwarzbier, oder nur sehr verunreinigtes Weißbier ist ...')

    );
	
	protected static $weight = 5;
}	