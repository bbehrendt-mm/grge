<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Splinter extends Model_Items_Abstract_Ammo implements Interface_Autotaker {

	protected static $static_info = Array(
			'name' => 'Splitterkugeln',
			'icon' => 'splinter',
			'description' => 'Splitterkugeln bestehen aus unter Hochdruck zusammengepresstem Müll. Du kannst sie in einen Splitterwerfer laden und damit auf Zombies schießen - beim Aufprall besteht eine Chance, dass die Splitterkugel explodiert und eine Menge Schaden an allen Zombies in der Umgebung anrichtet.',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_MISC,
	);

	protected static $weight = 0;
	
	protected static $autospawn = Array(2,5);
	protected static $autoappender = Array('Stück', 'Stück');
}	