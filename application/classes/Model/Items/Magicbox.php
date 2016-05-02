<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Magicbox extends Model_Items_Abstract_Item {
	
	protected static $static_info = Array(
			'name' => 'Magische Box',
			'icon' => 'magicbox',
			'description' => 'Dieses Item wurde zu Testzwecken implementiert und erlaubt es, beliebige andere Items zu erzeugen.',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_GEAR,
	);
	
	protected static $weight = 0;
	protected static $essential = true;
}	