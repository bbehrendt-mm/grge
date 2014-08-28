<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Battery extends Model_Items_Abstract_Ammo implements Interface_Autotaker {

	protected static $static_info = Array(
			'name' => 'Handvoll Batterien',
			'icon' => 'battery',
			'description' => 'Diese kleinen Teile kannst du benutzen um Elektrogeräte zu betreiben. Alternativ kannst du sie auch als Munition für einen Batteriewerfer verwenden.',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_MISC,
	);

	protected static $weight = 0;
	
	protected static $autospawn = Array(6,11);
	protected static $autoappender = Array('Stück', 'Stück');
}	