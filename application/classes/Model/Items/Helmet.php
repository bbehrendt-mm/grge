<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Helmet extends Model_Items_Abstract_Armor {
	
	protected static $static_info = Array(
			'name' => 'Fahradhelm',
			'icon' => 'helmet',
			'description' => 'Wusstest du, dass ein simpler Fahradhelm die Wahrscheinlichkeit eines Todes durch gehirnfressende Zombies um 23.956% reduziert?',
            'category' => Model_Items_Abstract_Item::MIAI_CAT_GEAR,
    );
	
	protected static $weight = 1;

    protected static $atype = Model_Items_Abstract_Armor::MIAA_HELMET;
    protected $protection = 30;
    protected static $damage_reduction = 0;
    protected static $damage_blocking = 1;
}	