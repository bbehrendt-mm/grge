<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Generic_Water1 extends Model_Items_Abstract_Liquid implements Interface_Static {

	protected static $static_info = Array(
			'name' => 'Dreckiges Wasser',
			'icon' => 'water_dirty',
			'description' => 'Dieses Wasser ist abgestanden, es haben sich sogar schon Algen darin gebildet. Du kannst es immer noch trinken, aber es wird wahrscheinlich deiner Gesundheit schaden.',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_FOOD,
	);
	
	public function __construct() {
		parent::__construct(Globals::CurrentGame()->config('items.water.tox_dirty'));
	}
}	