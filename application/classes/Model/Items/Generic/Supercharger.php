<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Generic_Supercharger extends Model_Items_Abstract_Item implements Interface_Static {
	
	protected static $static_info = Array(
			'name' => 'Supercharger-Batterie',
			'icon' => 'battery_super',
			'description' => 'Du hast diese Batterie so stark überladen, dass sie zu explodieren droht, deshalb musst du sie auch mit Samthandschuhen anfassen. Komm bloß nicht auf die Idee, diese Batterie zu den anderen in deinen Munitionsgürtel zu stecken oder gar mit einem Batteriewerfer abzufeuern - es sei denn, du möchtest dich gerne im Zentrum einer Pilzwolke wiederfinden.',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_RES,
	);

	protected static $weight = 1;
}