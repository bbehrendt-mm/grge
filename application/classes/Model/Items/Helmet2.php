<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Helmet2 extends Model_Items_Abstract_Armor {
	
	protected static $static_info = Array(
			'name' => 'Verstärkter Fahradhelm',
			'icon' => 'helmet2',
			'description' => 'Für einen normalen Radfahrer wären diese Upgrades ein ziemlicher Overkill - im Falle einer Zombieapokalypse kann man jedoch nicht vorsichtig genug sein.',
            'category' => Model_Items_Abstract_Item::MIAI_CAT_GEAR,
    );
	
	protected static $weight = 1;

    protected static $equipment_type = Model_Items_Abstract_Equipable::MIAE_ARMOR_HELMET;
    protected $protection = 50;
    protected static $damage_reduction = 0.2;
    protected static $damage_blocking = 1;
}	