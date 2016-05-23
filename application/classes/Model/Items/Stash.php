<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Stash extends Model_Items_Abstract_Box {

    protected static $config = 'bx_stash1';
    protected static $content = 2;

	protected static $static_info = Array(
			'name' => 'Kiste',
			'icon' => 'stash1',
			'description' => 'Diese Kiste sieht so aus, als würde sie nützliche Gegenstände enthalten. Warum schaust du nicht mal hinein?',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_MISC,
	);

	protected static $weight = 20;


}	