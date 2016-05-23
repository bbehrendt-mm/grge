<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Stash2 extends Model_Items_Abstract_Box {

    protected static $config = 'bx_stash2';
    protected static $content = 2;

	protected static $static_info = Array(
			'name' => 'Habseligkeiten eines Bürgers',
			'icon' => 'stash2',
			'description' => 'In dieser Kiste ist alles gesammelt, was man fürs erfolgreiche Überleben in der Postapokalypse benötigt.',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_MISC,
	);

	protected static $weight = 20;


}	