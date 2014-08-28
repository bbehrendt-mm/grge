<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Powderbomb extends Model_Items_Abstract_Escape implements Interface_Static {

	protected static $static_info = Array(
			'name' => 'Schwarzpulverbombe',
			'icon' => 'powderbomb',
			'description' => 'Aufgrund einer Kombination aus minderwertigem Schwarzpulver sowie deiner Unerfahrenheit im Bombenbau ist diese Schwarzpulverbombe leider nicht dafür geeignet, Zombies wegzusprengen. Sie wirbelt allerdings genug Staub auf, um Zombies zu verwirren und dir bei der Flucht vor ihnen zu helfen.',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_GEAR,
	);
	
	protected static $weight = 5;
	
	protected static $associated_view = 'escape';	
	protected static $esc_value = 10;
}	