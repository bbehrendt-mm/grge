<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Generic_Water0 extends Model_Items_Abstract_Liquid implements Interface_Static {

	protected static $static_info = Array(
			'name' => 'Klares Wasser',
			'icon' => 'water_clean',
			'description' => 'Klares Wasser zu finden ist eine Seltenheit - pass auf, dass du es nicht ruinierst indem du es mit dreckigem Wasser mischt!',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_FOOD,
	);
	
	public function __construct() {
		parent::__construct(0);
	}
}	