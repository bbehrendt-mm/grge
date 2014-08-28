<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Bottle extends Model_Items_Abstract_Bottle {
	
	protected static $static_info = Array(
			'name' => 'Feldflasche',
			'icon' => 'bottle',
			'description' => 'Diese kleine, pfadfindergeprüfte Feldflasche kann bis zu 4 Rationen Wasser ausnehmen. Außerdem lässt sie sich beschriften - es wäre doch schade, wenn du deine Wasserfeldflasche mal mit der Feldflasche verwechselst, in der du die Batteriesäure aufbewahrst... ',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_GEAR,
	);

	protected static $weight = 0;
	protected static $essential = true;
}	