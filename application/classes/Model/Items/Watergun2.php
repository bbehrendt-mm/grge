<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Watergun2 extends Model_Items_Abstract_Wbgun {
	
	protected static $static_info = Array(
			'name' => 'Aquablaster XL',
			'icon' => 'watergun2',
			'description' => 'Die Aquablaster XL ist die militärische Variante der Aquablaster XS. Durch das zusätzliche Hochleistungsprühsystem handelt es sich hierbei um eine tödliche Waffe (insbesondere für Zombies). Falls gerade keine Zombie-Apokalypse stattfindet, kann man sie auch zur Auflösung lästiger Demonstationen verwenden.',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_FIGHT,
	);

	protected static $capacity = 4;

	protected static $weight = 7;
	protected static $essential = true;

	protected static $damage = [15,20];
	protected static $range = [0,15];
	protected static $use_fixed_accuracy = false;
	protected static $aoe = true;

	protected static $accuracy_downscale = 0.5;
}	