<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Bolts extends Model_Items_Abstract_Ammo implements Interface_Autotaker {

	protected static $static_info = Array(
			'name' => 'Bolzen',
			'icon' => 'bolts',
			'description' => 'Du kannst diese kleinen Holzbolzen mit einer Armbrust abfeuern. Die werden nicht so viel Schaden anrichten wie eine Batterie, aber dafür kannst du sie leichter herstellen.',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_MISC,
	);

	protected static $weight = 0;

	protected static $autoappender = Array('Stück', 'Stück');
}	