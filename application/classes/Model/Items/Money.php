<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Money extends Model_Items_Abstract_Ammo implements Interface_Autotaker {
	
	protected static $static_info = Array(
			'name' => 'Geld',
			'icon' => 'money',
			'description' => 'So richtig viel kannst du mit diesem Geld nicht wirklich anfangen, immerhin haben die meisten Geschäfte hier in der Umgebung geschlossen. Aber hey, einem geschenkten Gaul haut nicht aufs Maul... oder so. Steck die Kohle einfach in deinen Munitionsgürtel, bis du etwas findest für dass du es ausgeben kannst.',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_MISC,
	);
	
	protected static $weight = 0;
	protected static $autospawn = Array(1,2);
	protected static $autoappender = Array('€', '€');
	protected static $boni = Array(1050 => Array(1 => 2, 2 => 2, 3 => 2, 4 => 2, 5 => 2));
	
}	