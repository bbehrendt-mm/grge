<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Stash3 extends Model_Items_Abstract_Box {

    protected static $config = 'bx_stash3';
    protected static $content = 2;

	protected static $static_info = Array(
			'name' => 'Habseligkeiten eines erfahrenen Bürgers',
			'icon' => 'stash3',
			'description' => 'Diese Kiste enthält seltene Items, die dir das Überleben in der Postapokalypse sicherlich erleichtern werden.',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_MISC,
	);

	protected static $weight = 25;


}	